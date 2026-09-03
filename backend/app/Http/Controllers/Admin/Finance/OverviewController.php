<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Enums\TransactionKind;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\FinanceStats;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * The money at a glance: one month, what came in, what went out, and whether
 * that is on course to stay inside the budget.
 *
 * The page answers "am I fine?" before it answers "on what?", which is why the
 * budget pace sits above the category breakdown rather than under it.
 */
class OverviewController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(Auth::user()?->can('finance.view'), 403);

        $user = Auth::user();
        $stats = new FinanceStats($user->id, $user->timezone);

        $month = CarbonImmutable::parse($request->input('month', $stats->today()->toDateString()))
            ->startOfMonth();

        $start = $month->startOfMonth();
        $end = $month->endOfMonth();

        return view('admin.finance.overview', [
            'month' => $month,
            'months' => $this->monthOptions($stats->today()),
            'summary' => $stats->summary($start, $end),
            'budget' => $stats->budgetStatus($month),
            'byCategory' => $stats->byCategory($start, $end),
            'byIncome' => $stats->byCategory($start, $end, TransactionKind::Income),
            'daily' => $stats->daily($start, $end),
            'monthly' => $stats->monthly(6, $month),
            'breaches' => $stats->breaches($month),
            'largest' => $stats->largest($start, $end),
            'recent' => Transaction::query()
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
