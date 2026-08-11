<?php

namespace App\Http\Controllers\Admin\Plan;

use App\Enums\FailReason;
use App\Enums\PlanEventType;
use App\Enums\PlanStatus;
use App\Enums\Priority;
use App\Http\Controllers\Controller;
use App\Models\Goal;
use App\Models\Plan;
use App\Services\PlanService;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Daily plans: the list, the form, and the three actions that close a plan.
 *
 * Closing a plan never happens here — that lives in PlanService, because the
 * Telegram bot and the scheduler close plans too and all three have to leave
 * the same trail behind.
 */
class PlanController extends Controller
{
    public function __construct(private readonly PlanService $plans) {}

    public function index(Request $request): View
    {
        $this->authorizeAction('view');

        $filters = [
            'date' => $request->input('date'),
            'status' => $request->input('status'),
            'goal' => $request->input('goal'),
            'priority' => $request->input('priority'),
        ];

        $query = Plan::query()
            ->where('user_id', Auth::id())
            ->with('goal:id,title,color');

        if ($filters['date']) {
            $query->forDate($filters['date']);
        }

        if ($filters['status']) {
            $query->where('status', $filters['status']);
        }

        if ($filters['goal']) {
            $query->where('goal_id', $filters['goal']);
        }

        if ($filters['priority']) {
            $query->where('priority', $filters['priority']);
        }

        $plans = $query
            ->orderByDesc('date')
            ->orderBy('start_time')
            ->paginate(25)
            ->withQueryString();

        return view('admin.plan.plans.index', [
            'plans' => $plans,
            'filters' => $filters,
            'goals' => $this->goalOptions(),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorizeAction('create');

        return view('admin.plan.plans.form', [
            'plan' => new Plan([
                'date' => $request->input('date', CarbonImmutable::today()->toDateString()),
                'start_time' => '09:00',
                'planned_minutes' => 60,
                'priority' => Priority::Medium,
            ]),
            'goals' => $this->goalOptions(),
            'action' => route('admin.plans.store'),
            'method' => 'POST',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAction('create');

        $this->plans->create($this->validated($request) + ['user_id' => Auth::id()]);

        return redirect()->route('admin.plans.index')->with('success', 'Plan created.');
    }

    public function show(Plan $plan): View
    {
        $this->authorizeAction('view');
        $this->authorizeOwnership($plan);

        return view('admin.plan.plans.show', [
            'plan' => $plan->load('goal', 'interruption'),
            'events' => $plan->events()->orderBy('created_at')->get(),
            'notifications' => $plan->notifications()->orderByDesc('created_at')->get(),
        ]);
    }

    public function edit(Plan $plan): View
    {
        $this->authorizeAction('edit');
        $this->authorizeOwnership($plan);

        return view('admin.plan.plans.form', [
            'plan' => $plan,
            'goals' => $this->goalOptions(),
            'action' => route('admin.plans.update', $plan),
            'method' => 'PUT',
        ]);
    }

    public function update(Request $request, Plan $plan): RedirectResponse
    {
        $this->authorizeAction('edit');
        $this->authorizeOwnership($plan);

        $this->plans->update($plan, $this->validated($request));

        return redirect()->route('admin.plans.index')->with('success', 'Plan saved.');
    }

    public function destroy(Plan $plan): RedirectResponse
    {
        $this->authorizeAction('delete');
        $this->authorizeOwnership($plan);

        $plan->delete();

        return redirect()->route('admin.plans.index')->with('success', 'Plan deleted.');
    }

    /** Complete, fail or postpone from the listing, without opening the form. */
    public function act(Request $request, Plan $plan, string $action): RedirectResponse
    {
        $this->authorizeAction('edit');
        $this->authorizeOwnership($plan);

        match ($action) {
            'complete' => $this->plans->complete($plan, $request->integer('actual_minutes') ?: null),
            'fail' => $this->plans->fail($plan, FailReason::tryFrom((string) $request->input('reason')) ?? FailReason::Other),
            'postpone' => $this->plans->postpone($plan, $request->integer('minutes') ?: 30),
            default => abort(404),
        };

        return back()->with('success', 'Plan updated.');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request): array
    {
        return $request->validate([
            'goal_id' => ['nullable', 'integer', 'exists:goals,id'],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'planned_minutes' => ['required', 'integer', 'min:5', 'max:1440'],
            'priority' => ['required', 'string', 'in:' . implode(',', array_column(Priority::cases(), 'value'))],
            'status' => ['required', 'string', 'in:' . implode(',', array_column(PlanStatus::cases(), 'value'))],
            'actual_minutes' => ['nullable', 'integer', 'min:0', 'max:1440'],
        ]);
    }

    /** @return array<int, string> */
    private function goalOptions(): array
    {
        return Goal::query()
            ->where('user_id', Auth::id())
            ->orderByDesc('month')
            ->orderBy('sort_order')
            ->get(['id', 'title', 'month'])
            ->mapWithKeys(fn (Goal $goal): array => [
                $goal->id => $goal->title . ' · ' . $goal->month->format('M Y'),
            ])
            ->all();
    }

    private function authorizeAction(string $action): void
    {
        abort_unless(Auth::user()?->can("plans.{$action}"), 403);
    }

    private function authorizeOwnership(Plan $plan): void
    {
        abort_unless($plan->user_id === Auth::id(), 403);
    }
}
