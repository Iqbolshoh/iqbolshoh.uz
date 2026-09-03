<?php

namespace Tests\Feature;

use App\Models\ForecastReport;
use App\Models\Goal;
use App\Models\Plan;
use App\Models\Transaction;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Columns that hold a day, and nothing finer.
 *
 * A bare `date` cast serialises back to "2026-09-03 00:00:00". MySQL truncates
 * that into its DATE column, so production reads correctly; SQLite keeps the
 * whole string, so the test suite compares "2026-09-03 00:00:00" against
 * "2026-09-03" as text — an exact match finds nothing, and a range silently
 * drops its own last day. Production right, tests wrong, nothing raised.
 *
 * These assert the stored shape rather than any one query, because the shape is
 * what every query downstream depends on.
 */
class DateColumnsTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private CarbonImmutable $today;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['timezone' => 'Asia/Samarkand']);
        $this->today = CarbonImmutable::today($this->user->timezone);
    }

    public function test_a_day_column_is_stored_as_a_day(): void
    {
        foreach ($this->rows() as $label => $row) {
            [$model, $column] = $row;

            $this->assertSame(
                $this->today->startOfMonth()->toDateString(),
                $model->getRawOriginal($column),
                "{$label} stores more than a day, which breaks every comparison against one"
            );
        }
    }

    /** An exact match on the month, which a stored timestamp never satisfies. */
    public function test_a_month_can_be_looked_up_by_its_own_value(): void
    {
        $this->goal();

        $this->assertSame(
            1,
            Goal::query()->where('month', $this->today->startOfMonth()->toDateString())->count()
        );
    }

    /** The last day of a range belongs to it. */
    public function test_a_range_contains_its_final_day(): void
    {
        foreach ([$this->today->startOfMonth(), $this->today->endOfMonth()] as $date) {
            $this->plan($date);
            $this->transaction($date);
        }

        $start = $this->today->startOfMonth();
        $end = $this->today->endOfMonth();

        $this->assertSame(2, Plan::query()->between($start, $end)->count(), 'a plan on the last day fell out of the month');
        $this->assertSame(2, Transaction::query()->between($start, $end)->count(), 'a transaction on the last day fell out of the month');
    }

    /** @return array<string, array{0: \Illuminate\Database\Eloquent\Model, 1: string}> */
    private function rows(): array
    {
        $first = $this->today->startOfMonth();

        return [
            'plans.date' => [$this->plan($first), 'date'],
            'transactions.date' => [$this->transaction($first), 'date'],
            'goals.month' => [$this->goal(), 'month'],
            'forecast_reports.month' => [ForecastReport::query()->create([
                'user_id' => $this->user->id,
                'month' => $first->toDateString(),
                'generated_at' => now(),
            ]), 'month'],
        ];
    }

    private function plan(CarbonImmutable $date): Plan
    {
        return Plan::query()->create([
            'user_id' => $this->user->id,
            'title' => 'A plan',
            'date' => $date->toDateString(),
            'start_time' => '09:00:00',
            'planned_minutes' => 30,
            'status' => 'pending',
        ]);
    }

    private function transaction(CarbonImmutable $date): Transaction
    {
        return Transaction::query()->create([
            'user_id' => $this->user->id,
            'kind' => 'expense',
            'amount' => 1000,
            'date' => $date->toDateString(),
        ]);
    }

    private function goal(): Goal
    {
        return Goal::query()->create([
            'user_id' => $this->user->id,
            'title' => 'A goal',
            'month' => $this->today->startOfMonth()->toDateString(),
        ]);
    }
}
