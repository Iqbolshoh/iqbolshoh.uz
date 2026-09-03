<?php

namespace Tests\Feature\Finance;

use App\Enums\TransactionKind;
use App\Models\FinanceCategory;
use App\Models\User;
use App\Services\FinanceService;
use App\Services\FinanceStats;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The numbers behind the charts.
 *
 * Both failures guarded here come from the same place: `kind` is a cast enum,
 * and code that forgets it either crashes (an enum cannot be an array key) or,
 * worse, quietly compares an enum to a string, never matches, and draws a chart
 * of zeroes that looks exactly like a month with no spending.
 *
 * The distinction matters for what to assert: a series has to carry the real
 * totals, not merely exist.
 */
class FinanceStatsTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private FinanceStats $stats;

    private CarbonImmutable $today;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['timezone' => 'Asia/Samarkand']);
        app(FinanceService::class)->ensureDefaults($this->user);

        $this->today = CarbonImmutable::today($this->user->timezone);
        $this->stats = new FinanceStats($this->user->id, $this->user->timezone);

        $this->record(TransactionKind::Expense, 25000, 'food');
        $this->record(TransactionKind::Expense, 15000, 'transport');
        $this->record(TransactionKind::Income, 900000, 'salary');
    }

    public function test_the_daily_series_carries_the_days_totals(): void
    {
        $series = $this->stats->daily($this->today->startOfMonth(), $this->today->endOfMonth());

        $day = $series->get($this->today->toDateString());

        $this->assertSame(40000, $day['expense'], 'the day lost its expenses');
        $this->assertSame(900000, $day['income'], 'the day lost its income');
    }

    /**
     * A day with nothing on it is present and zero, not missing.
     *
     * The gap is the point: a chart that skips empty days draws a straight line
     * through them and hides exactly the pattern the page exists to show.
     */
    public function test_the_daily_series_is_zero_filled(): void
    {
        $start = $this->today->startOfMonth();
        $end = $this->today->endOfMonth();

        $series = $this->stats->daily($start, $end);

        $this->assertSame($end->day, $series->count(), 'the month is missing days');

        $quiet = $series->reject(fn (array $day, string $date): bool => $date === $this->today->toDateString());

        foreach ($quiet as $date => $day) {
            $this->assertSame(['income' => 0, 'expense' => 0], $day, "{$date} should be an empty day");
        }
    }

    public function test_the_monthly_series_carries_this_months_totals(): void
    {
        $series = $this->stats->monthly(6, $this->today);

        $month = $series->get($this->today->format('Y-m'));

        $this->assertSame(40000, $month['expense'], 'the month lost its expenses');
        $this->assertSame(900000, $month['income'], 'the month lost its income');
        $this->assertSame(860000, $month['balance']);
    }

    public function test_the_summary_and_the_series_agree(): void
    {
        $summary = $this->stats->summary($this->today->startOfMonth(), $this->today->endOfMonth());
        $month = $this->stats->monthly(6, $this->today)->get($this->today->format('Y-m'));

        $this->assertSame($summary['expense'], $month['expense']);
        $this->assertSame($summary['income'], $month['income']);
    }

    private function record(TransactionKind $kind, int $amount, string $categoryKey): void
    {
        app(FinanceService::class)->record(
            user: $this->user,
            kind: $kind,
            amount: $amount,
            category: FinanceCategory::query()
                ->where('user_id', $this->user->id)
                ->where('key', $categoryKey)
                ->first(),
        );
    }
}
