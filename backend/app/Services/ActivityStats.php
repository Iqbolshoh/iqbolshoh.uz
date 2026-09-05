<?php

namespace App\Services;

use App\Models\ActivityCategory;
use App\Models\ActivityEntry;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

/**
 * Reading the time log back: a day, a week, a month.
 *
 * The one number that makes the rest of it mean anything is coverage — how
 * much of the period was accounted for at all. Twelve hours of work sounds
 * like a hard day until you notice only fourteen hours were logged; the same
 * twelve out of a fully-logged week is a different fact entirely.
 */
class ActivityStats
{
    public function __construct(
        private readonly int $userId,
        private readonly string $timezone = 'UTC',
    ) {}

    public function today(): CarbonImmutable
    {
        return CarbonImmutable::today($this->timezone);
    }

    /**
     * The period as a whole.
     *
     * `covered` is the share of the period's real minutes that carry an entry,
     * capped at 100: a day can be over-logged (two things at once, or a
     * rounded-up estimate) and a bar past the end of its track reads as a
     * rendering fault rather than as the honest "roughly all of it" it is.
     *
     * @return array{minutes: int, count: int, days: int, covered: float, average: int}
     */
    public function summary(CarbonInterface $from, CarbonInterface $to): array
    {
        $rows = ActivityEntry::query()
            ->where('user_id', $this->userId)
            ->between($from, $to)
            ->selectRaw('COALESCE(SUM(minutes), 0) as minutes, COUNT(*) as count')
            ->first();

        $minutes = (int) ($rows->minutes ?? 0);
        $days = max(1, $from->diffInDays($to) + 1);
        $possible = $days * ActivityService::DAY_MINUTES;

        return [
            'minutes' => $minutes,
            'count' => (int) ($rows->count ?? 0),
            'days' => $days,
            'covered' => min(100.0, round($minutes / $possible * 100, 1)),
            'average' => (int) round($minutes / $days),
        ];
    }

    /**
     * Where the time went, biggest first.
     *
     * `share` is of the logged total rather than of the period, because that is
     * the question this list answers — "of the time I wrote down, how much was
     * work" — and coverage is reported separately so the two never get
     * confused for each other.
     *
     * @return Collection<int, array{category: ?ActivityCategory, minutes: int, share: float, count: int}>
     */
    public function byCategory(CarbonInterface $from, CarbonInterface $to): Collection
    {
        $rows = ActivityEntry::query()
            ->where('user_id', $this->userId)
            ->between($from, $to)
            ->selectRaw('category_id, SUM(minutes) as minutes, COUNT(*) as count')
            ->groupBy('category_id')
            ->orderByDesc('minutes')
            ->get();

        $total = max(1, (int) $rows->sum('minutes'));

        $categories = ActivityCategory::query()
            ->whereIn('id', $rows->pluck('category_id')->filter())
            ->get()
            ->keyBy('id');

        return $rows->map(fn ($row): array => [
            'category' => $row->category_id === null ? null : $categories->get($row->category_id),
            'minutes' => (int) $row->minutes,
            'count' => (int) $row->count,
            'share' => round((int) $row->minutes / $total * 100, 1),
        ]);
    }

    /**
     * Day by day, for a chart. Every day in the range appears, including the
     * empty ones — a gap that silently closes up turns a missed Tuesday into a
     * shorter week.
     *
     * @return Collection<int, array{date: string, minutes: int}>
     */
    public function daily(CarbonInterface $from, CarbonInterface $to): Collection
    {
        $rows = ActivityEntry::query()
            ->where('user_id', $this->userId)
            ->between($from, $to)
            ->selectRaw('date, SUM(minutes) as minutes')
            ->groupBy('date')
            ->pluck('minutes', 'date');

        $days = collect();

        for ($day = CarbonImmutable::parse($from); $day <= $to; $day = $day->addDay()) {
            $key = $day->toDateString();

            $days->push(['date' => $key, 'minutes' => (int) ($rows[$key] ?? 0)]);
        }

        return $days;
    }

    /**
     * The activities that are worth a word: the ones with a daily target, and
     * how the period actually went against it.
     *
     * A target is per day, so a week's is seven of them — comparing a week's
     * total against a daily figure is the kind of arithmetic that makes a
     * report untrustworthy the first time someone checks it.
     *
     * @return Collection<int, array{category: ActivityCategory, minutes: int, target: int, share: float}>
     */
    public function againstTargets(CarbonInterface $from, CarbonInterface $to): Collection
    {
        $days = max(1, $from->diffInDays($to) + 1);

        $minutes = ActivityEntry::query()
            ->where('user_id', $this->userId)
            ->between($from, $to)
            ->selectRaw('category_id, SUM(minutes) as minutes')
            ->groupBy('category_id')
            ->pluck('minutes', 'category_id');

        return ActivityCategory::query()
            ->where('user_id', $this->userId)
            ->active()
            ->whereNotNull('daily_target_minutes')
            ->orderBy('sort_order')
            ->get()
            ->map(function (ActivityCategory $category) use ($minutes, $days): array {
                $target = (int) $category->daily_target_minutes * $days;
                $spent = (int) ($minutes[$category->id] ?? 0);

                return [
                    'category' => $category,
                    'minutes' => $spent,
                    'target' => $target,
                    'share' => $target > 0 ? round($spent / $target * 100, 1) : 0.0,
                ];
            });
    }
}
