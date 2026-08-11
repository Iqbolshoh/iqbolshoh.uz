<?php

namespace App\Http\Controllers\Admin\Plan;

use App\Enums\GoalStatus;
use App\Enums\Priority;
use App\Http\Controllers\Controller;
use App\Models\Goal;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Monthly goals. Everything is scoped to the signed-in account, so the same
 * code works unchanged the day a second person gets an account.
 */
class GoalController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeAction('view');

        $month = CarbonImmutable::parse($request->input('month', 'now'))->startOfMonth();

        $goals = Goal::query()
            ->where('user_id', Auth::id())
            ->forMonth($month)
            ->withCount('plans')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('admin.plan.goals.index', [
            'goals' => $goals,
            'month' => $month,
            'months' => $this->monthOptions(),
        ]);
    }

    public function create(): View
    {
        $this->authorizeAction('create');

        return view('admin.plan.goals.form', [
            'goal' => new Goal(['month' => CarbonImmutable::today()->startOfMonth()]),
            'action' => route('admin.goals.store'),
            'method' => 'POST',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAction('create');

        Goal::query()->create($this->validated($request) + ['user_id' => Auth::id()]);

        return redirect()->route('admin.goals.index')->with('success', 'Goal created.');
    }

    public function edit(Goal $goal): View
    {
        $this->authorizeAction('edit');
        $this->authorizeOwnership($goal);

        return view('admin.plan.goals.form', [
            'goal' => $goal,
            'action' => route('admin.goals.update', $goal),
            'method' => 'PUT',
        ]);
    }

    public function update(Request $request, Goal $goal): RedirectResponse
    {
        $this->authorizeAction('edit');
        $this->authorizeOwnership($goal);

        $goal->update($this->validated($request));

        return redirect()->route('admin.goals.index', ['month' => $goal->month->toDateString()])
            ->with('success', 'Goal saved.');
    }

    public function destroy(Goal $goal): RedirectResponse
    {
        $this->authorizeAction('delete');
        $this->authorizeOwnership($goal);

        $goal->delete();

        return redirect()->route('admin.goals.index')->with('success', 'Goal deleted.');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request): array
    {
        return $request->validate([
            'month' => ['required', 'date'],
            'title' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:2000'],
            'target' => ['nullable', 'string', 'max:80'],
            'priority' => ['required', 'string', 'in:' . $this->values(Priority::cases())],
            'status' => ['required', 'string', 'in:' . $this->values(GoalStatus::cases())],
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }

    /** @param  array<int, \BackedEnum>  $cases */
    private function values(array $cases): string
    {
        return implode(',', array_column($cases, 'value'));
    }

    /** @return array<string, string> */
    private function monthOptions(): array
    {
        $months = [];

        for ($offset = -6; $offset <= 2; $offset++) {
            $month = CarbonImmutable::today()->startOfMonth()->addMonths($offset);
            $months[$month->toDateString()] = $month->format('F Y');
        }

        return $months;
    }

    private function authorizeAction(string $action): void
    {
        abort_unless(Auth::user()?->can("goals.{$action}"), 403);
    }

    private function authorizeOwnership(Goal $goal): void
    {
        abort_unless($goal->user_id === Auth::id(), 403);
    }
}
