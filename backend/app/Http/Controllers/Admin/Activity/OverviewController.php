<?php

namespace App\Http\Controllers\Admin\Activity;

use App\Http\Controllers\Controller;
use App\Models\ActivityEntry;
use App\Services\ActivityService;
use App\Services\ActivityStats;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * The month at a glance: where the hours went, and how much of the month was
 * accounted for at all.
 *
 * Coverage sits above the breakdown for the same reason the budget pace sits
 * above the category list on the money page — it is the number that decides
 * whether the rest of the page can be trusted.
 */
class OverviewController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(Auth::user()?->can('activities.view'), 403);

        $user = Auth::user();
        $stats = new ActivityStats($user->id, $user->timezone);

        $month = CarbonImmutable::parse($request->input('month', $stats->today()->toDateString()))
            ->startOfMonth();

        $start = $month->startOfMonth();
        $end = $month->endOfMonth();
        $today = $stats->today();

        $byCategory = $stats->byCategory($start, $end);
        $good = (int) $byCategory->filter(fn (array $row): bool => $row['category']?->is_good ?? true)->sum('minutes');

        return view('admin.activity.overview', [
            'month' => $month,
            'months' => $this->monthOptions($today),
            'summary' => $stats->summary($start, $end),
            'todaySummary' => $stats->summary($today, $today),
            'weekSummary' => $stats->summary($today->startOfWeek(), $today->endOfWeek()),
            'byCategory' => $byCategory,
            'good' => $good,
            'bad' => (int) $byCategory->sum('minutes') - $good,
            'daily' => $stats->daily($start, $end),
            'targets' => $stats->againstTargets($start, $end),
            'dayMinutes' => ActivityService::DAY_MINUTES,
            'recent' => ActivityEntry::query()
                ->where('user_id', $user->id)
                ->with('category')
                ->orderByDesc('date')
                ->orderByDesc('id')
                ->limit(8)
                ->get(),
        ]);
    }

    /** @return array<string, string> */
    private function monthOptions(CarbonImmutable $today): array
    {
        $months = [];

        for ($offset = -11; $offset <= 1; $offset++) {
            $month = $today->startOfMonth()->addMonths($offset);
            $months[$month->toDateString()] = $month->format('F Y');
        }

        return $months;
    }
}
