<?php

namespace App\Http\Controllers\Admin\Plan;

use App\Http\Controllers\Controller;
use App\Services\Forecaster;
use App\Services\PlanStats;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/** Next month, projected from the month that just ended. */
class ForecastController extends Controller
{
    public function index(): View
    {
        abort_unless(Auth::user()?->can('forecast.view'), 403);

        $source = $this->sourceMonth();
        $forecaster = new Forecaster((int) Auth::id(), new PlanStats((int) Auth::id()));

        return view('admin.plan.forecast', [
            'source' => $source,
            'forecast' => $forecaster->build($source),
        ]);
    }

    /** Freeze the current projection so the numbers stop moving. */
    public function store(): RedirectResponse
    {
        abort_unless(Auth::user()?->can('forecast.view'), 403);

        (new Forecaster((int) Auth::id(), new PlanStats((int) Auth::id())))
            ->store($this->sourceMonth());

        return back()->with('success', 'Forecast saved.');
    }

    /**
     * Forecast from last month until this one is over — a month still in
     * progress would otherwise project from a handful of days.
     */
    private function sourceMonth(): CarbonImmutable
    {
        $today = CarbonImmutable::today();

        return $today->day >= 25
            ? $today->startOfMonth()
            : $today->subMonth()->startOfMonth();
    }
}
