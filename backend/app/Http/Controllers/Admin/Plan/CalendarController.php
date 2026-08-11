<?php

namespace App\Http\Controllers\Admin\Plan;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Services\PlanStats;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/** A month at a glance: how many plans each day held and how many landed. */
class CalendarController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(Auth::user()?->can('calendar.view'), 403);

        $month = CarbonImmutable::parse($request->input('month', 'now'))->startOfMonth();
        $stats = new PlanStats((int) Auth::id());

        $byDate = Plan::query()
            ->where('user_id', Auth::id())
            ->between($month, $month->endOfMonth())
            ->get(['id', 'date', 'status', 'planned_minutes'])
            ->groupBy(fn (Plan $plan): string => $plan->date->toDateString());

        $days = [];

        // The grid starts on the Monday of the week the month begins in, so the
        // leading blanks are real dates rather than empty placeholders.
        $cursor = $month->startOfWeek();
        $last = $month->endOfMonth()->endOfWeek();

        while ($cursor->lessThanOrEqualTo($last)) {
            $plans = $byDate->get($cursor->toDateString(), collect());
            $summary = $stats->summarise($plans);

            $days[] = [
                'date' => $cursor,
                'in_month' => $cursor->month === $month->month,
                'is_today' => $cursor->isToday(),
                'total' => $summary['total'],
                'completed' => $summary['completed'],
                'failed' => $summary['failed'] + $summary['no_response'],
                'rate' => $summary['raw_rate'],
                'minutes' => $summary['planned_minutes'],
            ];

            $cursor = $cursor->addDay();
        }

        return view('admin.plan.calendar', [
            'month' => $month,
            'days' => $days,
            'summary' => $stats->summary($month, $month->endOfMonth()),
        ]);
    }
}
