<?php

namespace App\Services;

use App\Models\ForecastReport;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

/**
 * Projects next month from what actually happened, and says how much to trust
 * the projection.
 *
 * Deliberately statistical rather than learned. With one or two months of
 * history a model has nothing to learn from, and a confident-looking number
 * built on twelve plans is worse than no number at all — so the confidence
 * level is derived from the sample size and reported alongside every figure.
 */
class Forecaster
{
    /** Below this many settled plans, the forecast refuses to commit. */
    private const MINIMUM_SAMPLE = 20;

    /**
     * Laplace smoothing. Three completed out of three is 100% only in the
     * arithmetic; smoothing pulls small samples back toward the overall rate so
     * a lucky week does not become a promise.
     */
    private const SMOOTHING = 4;

    public function __construct(
        private readonly int $userId,
        private readonly PlanStats $stats,
    ) {}

    /**
     * Build the forecast for the month after the one given.
     *
     * @return array{
     *     month: string, source: array, confidence: string,
     *     projection: list<array{plans: int, completed: int}>,
     *     segments: array, recommendations: list<string>, enough_data: bool
     * }
     */
    public function build(CarbonInterface $sourceMonth): array
    {
        $start = CarbonImmutable::parse($sourceMonth)->startOfMonth();
        $end = $start->endOfMonth();
        $target = $start->addMonth();

        $summary = $this->stats->summary($start, $end);
        $settled = $summary['total'] - $summary['pending'];
        $fair = $settled - $summary['interrupted'];

        $enough = $fair >= self::MINIMUM_SAMPLE;

        $rate = $fair > 0
            ? ($summary['completed'] + self::SMOOTHING * 0.7) / ($fair + self::SMOOTHING)
            : 0.0;

        $segments = [
            'hour_band' => $this->stats->byHourBand($start, $end),
            'weekday' => $this->stats->byWeekday($start, $end),
            'goal' => $this->stats->byGoal($start),
            'postponement' => $this->stats->byPostponement($start, $end),
        ];

        return [
            'month' => $target->toDateString(),
            'source' => $summary,
            'confidence' => $this->confidence($fair),
            'projection' => $enough ? $this->projection($rate) : [],
            'segments' => $segments,
            'recommendations' => $enough ? $this->recommendations($summary, $segments, $start, $end) : [],
            'enough_data' => $enough,
        ];
    }

    /** Persist the forecast so the number stops moving between page loads. */
    public function store(CarbonInterface $sourceMonth): ForecastReport
    {
        $forecast = $this->build($sourceMonth);

        return ForecastReport::query()->updateOrCreate(
            ['user_id' => $this->userId, 'month' => $forecast['month']],
            [
                'source_plans' => $forecast['source']['total'],
                'source_completed' => $forecast['source']['completed'],
                'raw_rate' => $forecast['source']['raw_rate'],
                'true_rate' => $forecast['source']['true_rate'],
                'confidence' => $forecast['confidence'],
                'projection' => $forecast['projection'],
                'segments' => $forecast['segments'],
                'recommendations' => $forecast['recommendations'],
                'generated_at' => now(),
            ]
        );
    }

    private function confidence(int $sample): string
    {
        return match (true) {
            $sample >= 80 => 'high',
            $sample >= self::MINIMUM_SAMPLE => 'medium',
            default => 'low',
        };
    }

    /** @return list<array{plans: int, completed: int}> */
    private function projection(float $rate): array
    {
        return array_map(
            fn (int $plans): array => ['plans' => $plans, 'completed' => (int) round($plans * $rate)],
            [80, 100, 120, 140]
        );
    }

    /**
     * Turn the segments into sentences worth acting on.
     *
     * Every line is tied to a number that came out of the data — a
     * recommendation nobody can trace back to their own history reads as filler
     * and gets ignored.
     *
     * @return list<string>
     */
    private function recommendations(
        array $summary,
        array $segments,
        CarbonImmutable $start,
        CarbonImmutable $end,
    ): array {
        $out = [];
        $days = max(1, $start->diffInDays(min($end, CarbonImmutable::today())) + 1);

        $perDayCompleted = round($summary['completed'] / $days, 1);
        $perDayPlanned = round($summary['total'] / $days, 1);

        $out[] = "💡 You completed an average of {$perDayCompleted} plans a day, while scheduling {$perDayPlanned}.";

        if ($perDayPlanned - $perDayCompleted >= 1.5) {
            $suggested = max(1, (int) floor($perDayCompleted) + 1);
            $out[] = "⚠️ That gap is where the failures come from. Try starting next month with {$suggested}–" . ($suggested + 1) . ' plans a day.';
        }

        $bands = array_values(array_filter($segments['hour_band'], fn (array $band): bool => $band['total'] >= 5));

        if ($bands !== []) {
            usort($bands, fn (array $a, array $b): int => $b['rate'] <=> $a['rate']);
            $best = $bands[0];
            $worst = end($bands);

            $out[] = "⏰ You finish {$best['rate']}% of what you schedule between {$best['label']}.";

            if ($best['rate'] - $worst['rate'] >= 15) {
                $out[] = "💡 Only {$worst['rate']}% of {$worst['label']} plans get done — move the important ones earlier.";
            }
        }

        $goals = array_values(array_filter($segments['goal'], fn (array $goal): bool => $goal['total'] >= 4));

        if ($goals !== []) {
            $weakest = end($goals);

            if ($weakest['rate'] < 60) {
                $out[] = "🎯 “{$weakest['title']}” is falling behind at {$weakest['rate']}%. Either cut its plans or move them to your strongest hours.";
            }
        }

        $postponed = collect($segments['postponement'])->firstWhere('label', 'Postponed 2+');

        if ($postponed && $postponed['total'] >= 5 && $postponed['rate'] < 50) {
            $out[] = "📋 Plans pushed twice or more only finish {$postponed['rate']}% of the time. When something slips twice, reschedule it for another day instead.";
        }

        if ($summary['interrupted'] > 0) {
            $out[] = "🏢 {$summary['interrupted']} plans were lost to interruptions, not to you — that is why the true rate is {$summary['true_rate']}% against a raw {$summary['raw_rate']}%.";
        }

        return $out;
    }
}
