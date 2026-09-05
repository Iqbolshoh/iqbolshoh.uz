<?php

namespace App\Http\Controllers\Admin\Activity;

use App\Enums\ActivitySource;
use App\Http\Controllers\Controller;
use App\Models\ActivityCategory;
use App\Models\ActivityEntry;
use App\Services\ActivityService;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * The time ledger: every stretch of the day, filterable and editable.
 *
 * The one place an entry the bot inferred from a status can be corrected or
 * removed — the bot deliberately refuses to touch those, because the owner did
 * not type them and would not expect them to vanish from a chat.
 */
class EntryController extends Controller
{
    public function __construct(private readonly ActivityService $activities) {}

    public function index(Request $request): View
    {
        $this->authorizeAction('view');

        $user = Auth::user();

        $filters = [
            'category' => $request->input('category'),
            'source' => $request->input('source'),
            'from' => $request->input('from'),
            'to' => $request->input('to'),
        ];

        $entries = ActivityEntry::query()
            ->where('user_id', $user->id)
            ->with('category')
            ->when($filters['category'], fn ($query, $id) => $query->where('category_id', $id))
            ->when($filters['source'], fn ($query, $source) => $query->where('source', $source))
            ->when($filters['from'], fn ($query, $from) => $query->where('date', '>=', $from))
            ->when($filters['to'], fn ($query, $to) => $query->where('date', '<=', $to))
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate(30)
            ->withQueryString();

        // The total is of everything the filter matches, not of the page — a
        // number that changes when you turn the page is worse than no number.
        $total = (int) ActivityEntry::query()
            ->where('user_id', $user->id)
            ->when($filters['category'], fn ($query, $id) => $query->where('category_id', $id))
            ->when($filters['source'], fn ($query, $source) => $query->where('source', $source))
            ->when($filters['from'], fn ($query, $from) => $query->where('date', '>=', $from))
            ->when($filters['to'], fn ($query, $to) => $query->where('date', '<=', $to))
            ->sum('minutes');

        return view('admin.activity.entries.index', [
            'entries' => $entries,
            'filters' => $filters,
            'total' => $total,
            'categories' => $this->activities->categories($user),
            'sources' => ActivitySource::cases(),
        ]);
    }

    public function create(): View
    {
        $this->authorizeAction('create');

        return view('admin.activity.entries.form', [
            'entry' => new ActivityEntry([
                'date' => CarbonImmutable::today(Auth::user()->timezone)->toDateString(),
                'source' => ActivitySource::Web,
            ]),
            'action' => route('admin.activities-entries.store'),
            'method' => 'POST',
            'categories' => $this->activities->categories(Auth::user()),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAction('create');

        ActivityEntry::query()->create($this->validated($request) + [
            'user_id' => Auth::id(),
            'source' => ActivitySource::Web->value,
        ]);

        return redirect()->route('admin.activities-entries.index')->with('success', 'Time entry added.');
    }

    public function edit(ActivityEntry $activitiesEntry): View
    {
        $this->authorizeAction('edit');
        $this->authorizeOwnership($activitiesEntry);

        return view('admin.activity.entries.form', [
            'entry' => $activitiesEntry,
            'action' => route('admin.activities-entries.update', $activitiesEntry),
            'method' => 'PUT',
            'categories' => $this->activities->categories(Auth::user()),
        ]);
    }

    public function update(Request $request, ActivityEntry $activitiesEntry): RedirectResponse
    {
        $this->authorizeAction('edit');
        $this->authorizeOwnership($activitiesEntry);

        $activitiesEntry->update($this->validated($request));

        return redirect()->route('admin.activities-entries.index')->with('success', 'Time entry saved.');
    }

    public function destroy(ActivityEntry $activitiesEntry): RedirectResponse
    {
        $this->authorizeAction('delete');
        $this->authorizeOwnership($activitiesEntry);

        $activitiesEntry->delete();

        return redirect()->route('admin.activities-entries.index')->with('success', 'Time entry deleted.');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request): array
    {
        return $request->validate([
            'category_id' => ['nullable', 'integer', 'exists:activity_categories,id'],
            // A day is the ceiling for one entry: past it the number is a typo
            // or a misread unit, and it would poison every average.
            'minutes' => ['required', 'integer', 'min:1', 'max:' . ActivityService::DAY_MINUTES],
            'date' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function authorizeAction(string $action): void
    {
        abort_unless(Auth::user()?->can("activities-entries.{$action}"), 403);
    }

    private function authorizeOwnership(ActivityEntry $entry): void
    {
        abort_unless($entry->user_id === Auth::id(), 403);
    }
}
