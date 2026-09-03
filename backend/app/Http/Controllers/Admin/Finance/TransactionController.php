<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Enums\PaymentMethod;
use App\Enums\TransactionKind;
use App\Enums\TransactionSource;
use App\Http\Controllers\Controller;
use App\Models\FinanceCategory;
use App\Models\Transaction;
use App\Services\FinanceStats;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * The ledger: every movement of money, filtered the way the owner is thinking
 * about it at that moment.
 *
 * The filtered totals are computed over the whole matching set, not the page
 * on screen. A total that only adds up the twenty rows currently visible is
 * worse than no total at all, because it looks authoritative.
 */
class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeAction('view');

        [$from, $to] = $this->range($request);

        $query = $this->filtered($request, $from, $to);

        $totals = (clone $query)
            ->selectRaw('kind, SUM(amount) AS total, COUNT(*) AS rows_count')
            ->groupBy('kind')
            ->get()
            ->keyBy('kind');

        $income = (int) ($totals[TransactionKind::Income->value]->total ?? 0);
        $expense = (int) ($totals[TransactionKind::Expense->value]->total ?? 0);

        return view('admin.finance.transactions.index', [
            'transactions' => $query->with('category')
                ->orderByDesc('date')
                ->orderByDesc('time')
                ->orderByDesc('id')
                ->paginate(25)
                ->withQueryString(),
            'categories' => $this->categories(),
            'filters' => $this->filters($request),
            'from' => $from,
            'to' => $to,
            'income' => $income,
            'expense' => $expense,
            'balance' => $income - $expense,
            'count' => (int) $totals->sum('rows_count'),
            'kinds' => TransactionKind::cases(),
            'methods' => PaymentMethod::cases(),
        ]);
    }

    public function create(): View
    {
        $this->authorizeAction('create');

        $user = Auth::user();

        return view('admin.finance.transactions.form', [
            'transaction' => new Transaction([
                'kind' => TransactionKind::Expense,
                'method' => PaymentMethod::Cash,
                'date' => CarbonImmutable::now($user->timezone)->toDateString(),
                'time' => CarbonImmutable::now($user->timezone)->format('H:i'),
            ]),
            'categories' => $this->categories(),
            'action' => route('admin.transactions.store'),
            'method' => 'POST',
            'kinds' => TransactionKind::cases(),
            'methods' => PaymentMethod::cases(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAction('create');

        Transaction::query()->create($this->validated($request) + [
            'user_id' => Auth::id(),
            'source' => TransactionSource::Web->value,
        ]);

        return redirect()
            ->route('admin.transactions.index')
            ->with('success', 'Transaction saved.');
    }

    public function edit(Transaction $transaction): View
    {
        $this->authorizeAction('edit');
        $this->authorizeOwnership($transaction);

        return view('admin.finance.transactions.form', [
            'transaction' => $transaction,
            'categories' => $this->categories(),
            'action' => route('admin.transactions.update', $transaction),
            'method' => 'PUT',
            'kinds' => TransactionKind::cases(),
            'methods' => PaymentMethod::cases(),
        ]);
    }

    public function update(Request $request, Transaction $transaction): RedirectResponse
    {
        $this->authorizeAction('edit');
        $this->authorizeOwnership($transaction);

        $transaction->update($this->validated($request));

        return redirect()
            ->route('admin.transactions.index')
            ->with('success', 'Transaction saved.');
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        $this->authorizeAction('delete');
        $this->authorizeOwnership($transaction);

        $transaction->delete();

        return redirect()
            ->route('admin.transactions.index')
            ->with('success', 'Transaction deleted.');
    }

    /**
     * The filtered set as a CSV, streamed rather than built in memory: an
     * export is the one place where "a few thousand rows" stops being true.
     */
    public function export(Request $request): StreamedResponse
    {
        $this->authorizeAction('view');

        [$from, $to] = $this->range($request);
        $query = $this->filtered($request, $from, $to)->with('category');

        $name = 'transactions-' . $from->format('Y-m-d') . '-to-' . $to->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($query): void {
            $handle = fopen('php://output', 'wb');

            // Excel opens a UTF-8 CSV as mojibake without the byte order mark,
            // and the category names here are not ASCII.
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['Date', 'Time', 'Kind', 'Category', 'Amount', 'Method', 'Source', 'Note']);

            $query->orderBy('date')->orderBy('id')->chunk(500, function ($rows) use ($handle): void {
                foreach ($rows as $row) {
                    fputcsv($handle, [
                        $row->date->format('Y-m-d'),
                        $row->time === null ? '' : substr((string) $row->time, 0, 5),
                        $row->kind->label(),
                        $row->category?->name ?? '',
                        $row->amount,
                        $row->method->label(),
                        $row->source->label(),
                        $row->note ?? '',
                    ]);
                }
            });

            fclose($handle);
        }, $name, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** The base query for both the listing and the export, so they can never disagree. */
    private function filtered(Request $request, CarbonImmutable $from, CarbonImmutable $to)
    {
        return Transaction::query()
            ->where('user_id', Auth::id())
            ->between($from, $to)
            ->when($request->filled('kind'), fn ($query) => $query->where('kind', $request->input('kind')))
            ->when($request->filled('category'), fn ($query) => $query->where('category_id', $request->input('category')))
            ->when($request->filled('method'), fn ($query) => $query->where('method', $request->input('method')))
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%' . $request->input('q') . '%';

                return $query->where('note', 'like', $term);
            });
    }

    /**
     * The date window. Named periods are resolved on the owner's clock, so
     * "today" means their today and not the server's.
     *
     * @return array{0: CarbonImmutable, 1: CarbonImmutable}
     */
    private function range(Request $request): array
    {
        $user = Auth::user();
        $today = CarbonImmutable::today($user->timezone);

        if ($request->filled('from') || $request->filled('to')) {
            $from = CarbonImmutable::parse($request->input('from', $today->startOfMonth()->toDateString()));
            $to = CarbonImmutable::parse($request->input('to', $today->toDateString()));

            // A window typed backwards is a typo, not an empty result.
            return $from <= $to ? [$from, $to] : [$to, $from];
        }

        return match ($request->input('period', 'month')) {
            'today' => [$today, $today],
            'week' => [$today->startOfWeek(), $today->endOfWeek()],
            'year' => [$today->startOfYear(), $today->endOfYear()],
            'all' => [CarbonImmutable::parse('2000-01-01'), $today->addYear()],
            default => [$today->startOfMonth(), $today->endOfMonth()],
        };
    }

    /** @return array<string, mixed> */
    private function filters(Request $request): array
    {
        return [
            'period' => $request->input('period', 'month'),
            'kind' => $request->input('kind'),
            'category' => $request->input('category'),
            'method' => $request->input('method'),
            'q' => $request->input('q'),
            'from' => $request->input('from'),
            'to' => $request->input('to'),
        ];
    }

    private function categories()
    {
        return FinanceCategory::query()
            ->where('user_id', Auth::id())
            ->orderBy('kind')
            ->orderBy('sort_order')
            ->get();
    }

    /** @return array<string, mixed> */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'kind' => ['required', 'string', 'in:' . implode(',', array_column(TransactionKind::cases(), 'value'))],
            // Whole so'm only. A decimal here would be silently floored by the
            // integer column, and the owner would never see which row lost it.
            'amount' => ['required', 'integer', 'min:1', 'max:999999999999'],
            'category_id' => ['nullable', 'integer', 'exists:finance_categories,id'],
            'date' => ['required', 'date'],
            'time' => ['nullable', 'date_format:H:i'],
            'note' => ['nullable', 'string', 'max:255'],
            'method' => ['required', 'string', 'in:' . implode(',', array_column(PaymentMethod::cases(), 'value'))],
        ]);

        // A category belonging to somebody else, or of the wrong direction,
        // would pass `exists` and then quietly distort every report.
        if (! empty($data['category_id'])) {
            $category = FinanceCategory::query()
                ->where('user_id', Auth::id())
                ->where('kind', $data['kind'])
                ->find($data['category_id']);

            $data['category_id'] = $category?->id;
        }

        return $data;
    }

    private function authorizeAction(string $action): void
    {
        abort_unless(Auth::user()?->can("transactions.{$action}"), 403);
    }

    private function authorizeOwnership(Transaction $transaction): void
    {
        abort_unless($transaction->user_id === Auth::id(), 403);
    }
}
