<?php

namespace App\Http\Controllers\Admin\Activity;

use App\Http\Controllers\Controller;
use App\Models\ActivityCategory;
use App\Services\ActivityService;
use App\Services\ActivityStats;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * The activities, their daily targets, and the words the bot recognises them
 * by.
 *
 * Keywords are editable here for the same reason they are on the money side:
 * the bot learns a word the moment a guess is corrected, and this is where the
 * owner can see what it has learned and take back anything it got wrong.
 */
class CategoryController extends Controller
{
    public function __construct(private readonly ActivityService $activities) {}

    public function index(): View
    {
        $this->authorizeAction('view');

        $user = Auth::user();
        $stats = new ActivityStats($user->id, $user->timezone);
        $month = $stats->today()->startOfMonth();

        $spent = $stats->byCategory($month, $month->endOfMonth())
            ->filter(fn (array $row): bool => $row['category'] !== null)
            ->keyBy(fn (array $row): int => $row['category']->id);

        return view('admin.activity.categories.index', [
            'categories' => ActivityCategory::query()
                ->where('user_id', $user->id)
                ->withCount('entries')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(),
            'spent' => $spent,
            'month' => $month,
        ]);
    }

    public function create(): View
    {
        $this->authorizeAction('create');

        return view('admin.activity.categories.form', [
            'category' => new ActivityCategory(['is_active' => true, 'is_good' => true]),
            'action' => route('admin.activities-categories.store'),
            'method' => 'POST',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAction('create');

        ActivityCategory::query()->create($this->validated($request) + ['user_id' => Auth::id()]);

        return redirect()->route('admin.activities-categories.index')->with('success', 'Activity created.');
    }

    public function edit(ActivityCategory $activitiesCategory): View
    {
        $this->authorizeAction('edit');
        $this->authorizeOwnership($activitiesCategory);

        return view('admin.activity.categories.form', [
            'category' => $activitiesCategory,
            'action' => route('admin.activities-categories.update', $activitiesCategory),
            'method' => 'PUT',
        ]);
    }

    public function update(Request $request, ActivityCategory $activitiesCategory): RedirectResponse
    {
        $this->authorizeAction('edit');
        $this->authorizeOwnership($activitiesCategory);

        $activitiesCategory->update($this->validated($request));

        return redirect()->route('admin.activities-categories.index')->with('success', 'Activity saved.');
    }

    /** Deleting an activity keeps its hours: the foreign key is nulled, not cascaded. */
    public function destroy(ActivityCategory $activitiesCategory): RedirectResponse
    {
        $this->authorizeAction('delete');
        $this->authorizeOwnership($activitiesCategory);

        $activitiesCategory->delete();

        return redirect()->route('admin.activities-categories.index')
            ->with('success', 'Activity deleted. Its entries are kept as uncategorised.');
    }

    /** Bring the account in line with the shipped list of activities. */
    public function restoreDefaults(): RedirectResponse
    {
        $this->authorizeAction('create');

        $result = $this->activities->syncDefaults(Auth::user());

        $parts = array_filter([
            $result['created'] ? "added {$result['created']}" : null,
            $result['updated'] ? "refreshed {$result['updated']}" : null,
        ]);

        return redirect()->route('admin.activities-categories.index')->with(
            'success',
            $parts === []
                ? 'Activities are already in step with the shipped list.'
                : 'Activities synced — ' . implode(', ', $parts) . '.'
        );
    }

    /** @return array<string, mixed> */
    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:60'],
            'icon' => ['nullable', 'string', 'max:16'],
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'keywords' => ['nullable', 'string', 'max:2000'],
            'daily_target_minutes' => ['nullable', 'integer', 'min:1', 'max:' . ActivityService::DAY_MINUTES],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]) + [
            'is_active' => $request->boolean('is_active'),
            'is_good' => $request->boolean('is_good'),
        ];
    }

    private function authorizeAction(string $action): void
    {
        abort_unless(Auth::user()?->can("activities-categories.{$action}"), 403);
    }

    private function authorizeOwnership(ActivityCategory $category): void
    {
        abort_unless($category->user_id === Auth::id(), 403);
    }
}
