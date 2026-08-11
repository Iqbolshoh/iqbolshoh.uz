<?php

namespace App\Http\Controllers\Admin\Plan;

use App\Http\Controllers\Controller;
use App\Services\PlanStats;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Daily, weekly and monthly views over the same numbers.
 *
 * They differ only in the window they look at, so one method builds all three
 * and the route decides the range.
 */
class AnalyticsController extends Controller
{
    public function daily(): View
    {
        return $this->render('daily', CarbonImmutable::today(), CarbonImmutable::today());
    }

    public function weekly(): View
    {
        $today = CarbonImmutable::today();

        return $this->render('weekly', $today->startOfWeek(), $today->endOfWeek());
    }

    public function monthly(): View
    {
        $today = CarbonImmutable::today();

        return $this->render('monthly', $today->startOfMonth(), $today->endOfMonth());
    }

    private function render(string $period, CarbonImmutable $from, CarbonImmutable $to): View
    {
        abort_unless(Auth::user()?->can('analytics.view'), 403);

        $stats = new PlanStats((int) Auth::id());

        return view('admin.plan.analytics', [
            'period' => $period,
            'from' => $from,
            'to' => $to,
            'summary' => $stats->summary($from, $to),
            'trend' => $stats->dailyTrend($period === 'monthly' ? 30 : 14),
            'plannedVsActual' => $stats->plannedVsActual($period === 'daily' ? 7 : 7),
            'hourBands' => $stats->byHourBand($from, $to),
            'weekdays' => $stats->byWeekday($from, $to),
            'goals' => $stats->byGoal($from),
            'postponement' => $stats->byPostponement($from, $to),
            'extremes' => $stats->extremes($from, $to),
        ]);
    }
}
