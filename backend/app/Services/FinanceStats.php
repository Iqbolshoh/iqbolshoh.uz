<?php

namespace App\Services;

use App\Enums\TransactionKind;
use App\Models\FinanceCategory;
use App\Models\FinanceSetting;
use App\Models\Transaction;
use App\Models\User;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Every number the finance pages, the bot and the reports are built from.
 *
 * All sums are done in SQL rather than in PHP: a year of daily spending is a
 * few thousand rows, and pulling them into memory to add them up is the kind
 * of thing that works fine for one month and then does not.
 */
class FinanceStats
{
    /**
     * @param  string|null  $timezone  the owner's wall clock; defaults to the
     *   account's own zone. Every "today" and "this month" in here is resolved
     *   in it, because the server runs on UTC and Samarkand is five hours
     *   ahead — between 19:00 and midnight local, UTC is still on yesterday,
     *   and a day's spending would land in the wrong day's total.
     */
    public function __construct(
        private readonly int $userId,
        private readonly ?string $timezone = null,
    ) {}

    /** The owner's zone, resolved once and cached for the life of the object. */
    private function zone(): string
    {
        return $this->timezone
            ?? User::find($this->userId)?->timezone
            ?? config('app.timezone');
    }

    /** Today, on the owner's wall clock. */
    public function today(): CarbonImmutable
    {
        return CarbonImmutable::today($this->zone());
    }

    /**
     * Income, expense and the balance between them for a date range.
     *
     * @return array{income: int, expense: int, balance: int, count: int, days: int, daily_average: int}
     */
    public function summary(CarbonInterface $from, CarbonInterface $to): array
    {
        $rows = Transaction::query()
            ->where('user_id', $this->userId)
            ->between($from, $to)
            ->selectRaw('kind, SUM(amount) AS total, COUNT(*) AS rows_count')
            ->groupBy('kind')
            ->get()
            ->keyBy('kind');

        $income = (int) ($rows[TransactionKind::Income->value]->total ?? 0);
        $expense = (int) ($rows[TransactionKind::Expense->value]->total ?? 0);
        $count = (int) $rows->sum('rows_count');

        // Averaged over the days that have already happened, not the whole
        // range: on the 3rd of the month, dividing by 31 would flatter the
        // number into meaninglessness.
        $days = $this->elapsedDays($from, $to);

        return [
            'income' => $income,
            'expense' => $expense,
            'balance' => $income - $expense,
            'count' => $count,
            'days' => $days,
            'daily_average' => $days > 0 ? intdiv($expense, $days) : 0,
        ];
    }

    /**
     * Spending per category for a range, biggest first, with each category's
     * share and how much of its monthly ceiling it has used.
     *
     * @return Collection<int, array{category: ?FinanceCategory, total: int, count: int, share: float, limit: ?int, limit_used: ?float}>
     */
    public function byCategory(CarbonInterface $from, CarbonInterface $to, TransactionKind $kind = TransactionKind::Expense): Collection
    {
        $rows = Transaction::query()
            ->where('user_id', $this->userId)
            ->ofKind($kind)
            ->between($from, $to)
            ->selectRaw('category_id, SUM(amount) AS total, COUNT(*) AS rows_count')
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->get();

        $total = (int) $rows->sum('total');

        $categories = FinanceCategory::query()
            ->where('user_id', $this->userId)
            ->whereIn('id', $rows->pluck('category_id')->filter())
            ->get()
            ->keyBy('id');

        return $rows->map(function ($row) use ($categories, $total): array {
            $category = $row->category_id === null ? null : $categories->get($row->category_id);
            $limit = $category?->monthly_limit;
            $sum = (int) $row->total;

            return [
                'category' => $category,
                'total' => $sum,
                'count' => (int) $row->rows_count,
                'share' => $total > 0 ? round($sum / $total * 100, 1) : 0.0,
                'limit' => $limit,
                'limit_used' => $limit > 0 ? round($sum / $limit * 100, 1) : null,
            ];
        });
    }

    /**
     * A total per day across the range, zero-filled.
     *
     * The gaps matter: a chart that silently skips the days with no spending
     * draws a flat line through them and hides exactly the pattern the owner
     * is looking for.
     *
     * @return Collection<string, array{income: int, expense: int}>
     */
    public function daily(CarbonInterface $from, CarbonInterface $to): Collection
    {
        $rows = Transaction::query()
            ->where('user_id', $this->userId)
            ->between($from, $to)
            ->selectRaw('date, kind, SUM(amount) AS total')
            ->groupBy('date', 'kind')
            ->get();

        $series = collect();
        $cursor = CarbonImmutable::parse($from)->startOfDay();
        $end = CarbonImmutable::parse($to)->startOfDay();

        while ($cursor <= $end) {
            $series->put($cursor->toDateString(), ['income' => 0, 'expense' => 0]);
            $cursor = $cursor->addDay();
        }

        foreach ($rows as $row) {
            $key = CarbonImmutable::parse($row->date)->toDateString();

            if (! $series->has($key)) {
                continue;
            }

            $day = $series->get($key);
            $day[$row->kind] = (int) $row->total;
            $series->put($key, $day);
        }

        return $series;
    }

    /**
     * A total per month for the last N months, oldest first.
     *
     * @return Collection<string, array{income: int, expense: int, balance: int}>
     */
    public function monthly(int $months = 6, ?CarbonInterface $endingAt = null): Collection
    {
        $end = CarbonImmutable::parse($endingAt ?? $this->today())->endOfMonth();
        $start = $end->startOfMonth()->subMonths(max(0, $months - 1));

        $rows = Transaction::query()
            ->where('user_id', $this->userId)
            ->between($start, $end)
            ->selectRaw($this->monthExpression() . ' AS period, kind, SUM(amount) AS total')
            ->groupBy('period', 'kind')
            ->get()
            ->groupBy('period');

        $series = collect();
        $cursor = $start;

        while ($cursor <= $end) {
            $key = $cursor->format('Y-m');
            $group = $rows->get($key, collect());

            $income = (int) ($group->firstWhere('kind', TransactionKind::Income->value)->total ?? 0);
            $expense = (int) ($group->firstWhere('kind', TransactionKind::Expense->value)->total ?? 0);

            $series->put($key, [
                'income' => $income,
                'expense' => $expense,
                'balance' => $income - $expense,
            ]);

            $cursor = $cursor->addMonth();
        }

        return $series;
    }

    /**
     * How the month is tracking against the budget, and whether that is on
     * course to hold: "spent 62% of the ceiling on day 12 of 30" is the number
     * worth acting on, not the raw total.
     *
     * @return array{budget: ?int, spent: int, left: ?int, used: ?float, pace: ?float, projected: int, over: bool}
     */
    public function budgetStatus(?CarbonInterface $inMonth = null): array
    {
        $month = CarbonImmutable::parse($inMonth ?? $this->today());
        $start = $month->startOfMonth();
        $end = $month->endOfMonth();

        $spent = (int) Transaction::query()
            ->where('user_id', $this->userId)
            ->ofKind(TransactionKind::Expense)
            ->between($start, $end)
            ->sum('amount');

        $budget = FinanceSetting::forUser($this->userId)->monthly_budget;

        $elapsed = $this->elapsedDays($start, $end);
        $inMonthDays = $end->day;
        $projected = $elapsed > 0 ? intdiv($spent, $elapsed) * $inMonthDays : 0;

        return [
            'budget' => $budget,
            'spent' => $spent,
            'left' => $budget === null ? null : $budget - $spent,
            'used' => $budget > 0 ? round($spent / $budget * 100, 1) : null,
            'pace' => $budget > 0 ? round($projected / $budget * 100, 1) : null,
            'projected' => $projected,
            'over' => $budget !== null && $spent > $budget,
        ];
    }

    /**
     * Categories that have already crossed the warning share of their ceiling
     * this month, worst first. Drives the panel banner and the bot's warning.
     *
     * @return Collection<int, array{category: FinanceCategory, total: int, limit: int, used: float}>
     */
    public function breaches(?CarbonInterface $inMonth = null): Collection
    {
        $month = CarbonImmutable::parse($inMonth ?? $this->today());
        $threshold = FinanceSetting::forUser($this->userId)->warn_at_percent;

        return $this->byCategory($month->startOfMonth(), $month->endOfMonth())
            ->filter(fn (array $row): bool => $row['limit'] > 0 && $row['limit_used'] >= $threshold)
            ->map(fn (array $row): array => [
                'category' => $row['category'],
                'total' => $row['total'],
                'limit' => $row['limit'],
                'used' => $row['limit_used'],
            ])
            ->sortByDesc('used')
            ->values();
    }

    /** The largest single expenses in a range. */
    public function largest(CarbonInterface $from, CarbonInterface $to, int $limit = 5): Collection
    {
        return Transaction::query()
            ->where('user_id', $this->userId)
            ->ofKind(TransactionKind::Expense)
            ->between($from, $to)
            ->with('category')
            ->orderByDesc('amount')
            ->limit($limit)
            ->get();
    }

    /**
     * The "YYYY-MM" of a row's date, in the dialect the current connection
     * speaks. Production is MySQL and the test suite is SQLite, and a
     * MySQL-only DATE_FORMAT here would make every month-over-month test fail
     * for a reason that has nothing to do with the money.
     */
    private function monthExpression(): string
    {
        return DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', `date`)"
            : "DATE_FORMAT(`date`, '%Y-%m')";
    }

    /**
     * Days in the range that have already happened, counting today. A range
     * entirely in the past is simply its own length.
     */
    private function elapsedDays(CarbonInterface $from, CarbonInterface $to): int
    {
        $start = CarbonImmutable::parse($from)->startOfDay();
        $end = CarbonImmutable::parse($to)->startOfDay();
        $today = $this->today();

        if ($end > $today) {
            $end = $today;
        }

        if ($end < $start) {
            return 0;
        }

        // diffInDays returns a float in Carbon 3; the +1 makes the range
        // inclusive of its first day.
        return (int) $start->diffInDays($end) + 1;
    }
}
