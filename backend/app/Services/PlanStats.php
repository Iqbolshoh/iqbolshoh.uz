<?php

namespace App\Services;

use App\Enums\PlanStatus;
use App\Models\Plan;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

/**
 * Every number the dashboard, analytics pages and forecast are built from.
 *
 * One rule runs through all of it: an interrupted plan is not a failure. It is
 * counted, shown and reported, but it is taken out of the denominator of the
 * "true" rate, because the owner did nothing wrong when a meeting ran long.
 */
class PlanStats
{
    public function __construct(private readonly int $userId) {}

    /**
     * Headline counts for a date range.
     *
     * @return array{
     *     total: int, completed: int, failed: int, postponed: int,
     *     interrupted: int, no_response: int, pending: int,
     *     planned_minutes: int, actual_minutes: int,
     *     raw_rate: float, true_rate: float
     * }
     */
    public function summary(CarbonInterface $from, CarbonInterface $to): array
    {
        $plans = Plan::query()
            ->where('user_id', $this->userId)
            ->between(CarbonImmutable::parse($from), CarbonImmutable::parse($to))
            ->get(['status', 'planned_minutes', 'actual_minutes']);

        return $this->summarise($plans);
    }

    /** @param  Collection<int, Plan>  $plans */
    public function summarise(Collection $plans): array
    {
        $count = fn (PlanStatus $status): int => $plans->where('status', $status)->count();

        $total = $plans->count();
        $completed = $count(PlanStatus::Completed);
        $interrupted = $count(PlanStatus::Interrupted);
        $pending = $count(PlanStatus::Pending) + $count(PlanStatus::InProgress);

        // Only settled plans can be scored: a plan whose time has not come yet
        // would otherwise drag today's rate down all morning.
        $settled = $total - $pending;
        $fair = $settled - $interrupted;

        return [
            'total' => $total,
            'completed' => $completed,
            'failed' => $count(PlanStatus::Failed),
            'postponed' => $count(PlanStatus::Postponed),
            'interrupted' => $interrupted,
            'no_response' => $count(PlanStatus::NoResponse),
            'pending' => $pending,
            'planned_minutes' => (int) $plans->sum('planned_minutes'),
            'actual_minutes' => (int) $plans->sum('actual_minutes'),
            'raw_rate' => $settled > 0 ? round($completed / $settled * 100, 1) : 0.0,
            'true_rate' => $fair > 0 ? round($completed / $fair * 100, 1) : 0.0,
        ];
    }

    /**
     * Completion rate per day, for the trend chart.
     *
     * @return list<array{date: string, label: string, rate: float, total: int, completed: int}>
     */
    public function dailyTrend(int $days = 30): array
    {
        $to = CarbonImmutable::today();
        $from = $to->subDays($days - 1);

        $byDate = Plan::query()
            ->where('user_id', $this->userId)
            ->between($from, $to)
            ->get(['date', 'status'])
            ->groupBy(fn (Plan $plan): string => $plan->date->toDateString());

        $out = [];

        for ($day = $from; $day->lessThanOrEqualTo($to); $day = $day->addDay()) {
            $plans = $byDate->get($day->toDateString(), collect());
            $summary = $this->summarise($plans);

            $out[] = [
                'date' => $day->toDateString(),
                'label' => $day->format('M j'),
                'rate' => $summary['raw_rate'],
                'total' => $summary['total'],
                'completed' => $summary['completed'],
            ];
        }

        return $out;
    }

    /**
     * Planned against actual minutes per day, for the bar chart.
     *
     * @return list<array{label: string, planned: float, actual: float}>
     */
    public function plannedVsActual(int $days = 7): array
    {
        $to = CarbonImmutable::today();
        $from = $to->subDays($days - 1);

        $byDate = Plan::query()
            ->where('user_id', $this->userId)
            ->between($from, $to)
            ->get(['date', 'planned_minutes', 'actual_minutes'])
            ->groupBy(fn (Plan $plan): string => $plan->date->toDateString());

        $out = [];

        for ($day = $from; $day->lessThanOrEqualTo($to); $day = $day->addDay()) {
            $plans = $byDate->get($day->toDateString(), collect());

            $out[] = [
                'label' => $day->format('D'),
                'planned' => round($plans->sum('planned_minutes') / 60, 1),
                'actual' => round($plans->sum('actual_minutes') / 60, 1),
            ];
        }

        return $out;
    }

    /**
     * Completion rate split by time of day — the segment that most often turns
     * into an actionable recommendation.
     *
     * @return list<array{key: string, label: string, rate: float, total: int}>
     */
    public function byHourBand(CarbonInterface $from, CarbonInterface $to): array
    {
        $bands = [
            'morning' => ['06:00–13:00', 6, 13],
            'afternoon' => ['13:00–18:00', 13, 18],
            'evening' => ['18:00–23:00', 18, 23],
        ];

        $plans = Plan::query()
            ->where('user_id', $this->userId)
            ->between(CarbonImmutable::parse($from), CarbonImmutable::parse($to))
            ->get(['start_time', 'status']);

        $out = [];

        foreach ($bands as $key => [$label, $startHour, $endHour]) {
            $inBand = $plans->filter(function (Plan $plan) use ($startHour, $endHour): bool {
                $hour = (int) substr((string) $plan->start_time, 0, 2);

                return $hour >= $startHour && $hour < $endHour;
            });

            $summary = $this->summarise($inBand);

            $out[] = [
                'key' => $key,
                'label' => $label,
                'rate' => $summary['raw_rate'],
                'total' => $summary['total'],
            ];
        }

        return $out;
    }

    /**
     * Completion rate per weekday, Monday first.
     *
     * @return list<array{label: string, rate: float, total: int}>
     */
    public function byWeekday(CarbonInterface $from, CarbonInterface $to): array
    {
        $plans = Plan::query()
            ->where('user_id', $this->userId)
            ->between(CarbonImmutable::parse($from), CarbonImmutable::parse($to))
            ->get(['date', 'status'])
            ->groupBy(fn (Plan $plan): int => $plan->date->dayOfWeekIso);

        $out = [];

        for ($iso = 1; $iso <= 7; $iso++) {
            $summary = $this->summarise($plans->get($iso, collect()));

            $out[] = [
                'label' => CarbonImmutable::now()->startOfWeek()->addDays($iso - 1)->format('D'),
                'rate' => $summary['raw_rate'],
                'total' => $summary['total'],
            ];
        }

        return $out;
    }

    /**
     * Completion rate per goal for a month.
     *
     * @return list<array{id: int, title: string, color: ?string, rate: float, total: int, completed: int}>
     */
    public function byGoal(CarbonInterface $month): array
    {
        $start = CarbonImmutable::parse($month)->startOfMonth();

        $plans = Plan::query()
            ->where('user_id', $this->userId)
            ->whereNotNull('goal_id')
            ->between($start, $start->endOfMonth())
            ->with('goal:id,title,color')
            ->get(['goal_id', 'status'])
            ->groupBy('goal_id');

        $out = [];

        foreach ($plans as $group) {
            $goal = $group->first()->goal;

            if ($goal === null) {
                continue;
            }

            $summary = $this->summarise($group);

            $out[] = [
                'id' => $goal->id,
                'title' => $goal->title,
                'color' => $goal->color,
                'rate' => $summary['raw_rate'],
                'total' => $summary['total'],
                'completed' => $summary['completed'],
            ];
        }

        usort($out, fn (array $a, array $b): int => $b['rate'] <=> $a['rate']);

        return $out;
    }

    /**
     * Completion rate split by how often a plan was pushed. The interesting
     * finding is usually how steeply it drops after the second postponement.
     *
     * @return list<array{label: string, rate: float, total: int}>
     */
    public function byPostponement(CarbonInterface $from, CarbonInterface $to): array
    {
        $plans = Plan::query()
            ->where('user_id', $this->userId)
            ->between(CarbonImmutable::parse($from), CarbonImmutable::parse($to))
            ->get(['postpone_count', 'status']);

        $buckets = [
            'Never postponed' => fn (Plan $plan): bool => $plan->postpone_count === 0,
            'Postponed once' => fn (Plan $plan): bool => $plan->postpone_count === 1,
            'Postponed 2+' => fn (Plan $plan): bool => $plan->postpone_count >= 2,
        ];

        $out = [];

        foreach ($buckets as $label => $matches) {
            $summary = $this->summarise($plans->filter($matches));

            $out[] = [
                'label' => $label,
                'rate' => $summary['raw_rate'],
                'total' => $summary['total'],
            ];
        }

        return $out;
    }

    /** Best and weakest weekday in a range, or null when there is nothing to compare. */
    public function extremes(CarbonInterface $from, CarbonInterface $to): array
    {
        $days = array_values(array_filter(
            $this->byWeekday($from, $to),
            fn (array $day): bool => $day['total'] > 0
        ));

        if ($days === []) {
            return ['best' => null, 'weakest' => null];
        }

        usort($days, fn (array $a, array $b): int => $b['rate'] <=> $a['rate']);

        return [
            'best' => $days[0],
            'weakest' => end($days),
        ];
    }
}
