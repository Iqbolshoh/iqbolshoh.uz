<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Enums\TransactionKind;
use App\Http\Controllers\Controller;
use App\Models\FinanceCategory;
use App\Services\FinanceService;
use App\Services\FinanceStats;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * The buckets, their monthly ceilings, and the words the bot recognises them
 * by.
 *
 * Keywords are editable here on purpose: the bot learns a word the moment the
 * owner corrects it, and this page is where they can see what it has learned
 * and take back anything it got wrong.
 */
class CategoryController extends Controller
{
    public function __construct(private readonly FinanceService $finance) {}

    public function index(): View
    {
        $this->authorizeAction('view');

        $user = Auth::user();
        $stats = new FinanceStats($user->id, $user->timezone);
        $month = $stats->today()->startOfMonth();

        // This month's spend per category, so a ceiling is shown next to the
        // number it is actually holding back.
        $spent = $stats->byCategory($month, $month->endOfMonth())
            ->filter(fn (array $row): bool => $row['category'] !== null)
            ->keyBy(fn (array $row): int => $row['category']->id);

        return view('admin.finance.categories.index', [
            'categories' => FinanceCategory::query()
                ->where('user_id', $user->id)
                ->withCount('transactions')
                ->orderBy('kind')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
                ->groupBy(fn (FinanceCategory $category): string => $category->kind->value),
            'spent' => $spent,
            'month' => $month,
        ]);
    }

    public function create(): View
    {
        $this->authorizeAction('create');

        return view('admin.finance.categories.form', [
            'category' => new FinanceCategory(['kind' => TransactionKind::Expense, 'is_active' => true]),
            'action' => route('admin.finance-categories.store'),
            'method' => 'POST',
            'kinds' => TransactionKind::cases(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAction('create');

        FinanceCategory::query()->create($this->validated($request) + ['user_id' => Auth::id()]);

        return redirect()->route('admin.finance-categories.index')->with('success', 'Category created.');
    }

    public function edit(FinanceCategory $financeCategory): View
    {
        $this->authorizeAction('edit');
        $this->authorizeOwnership($financeCategory);

        return view('admin.finance.categories.form', [
            'category' => $financeCategory,
            'action' => route('admin.finance-categories.update', $financeCategory),
            'method' => 'PUT',
            'kinds' => TransactionKind::cases(),
        ]);
    }

    public function update(Request $request, FinanceCategory $financeCategory): RedirectResponse
    {
        $this->authorizeAction('edit');
        $this->authorizeOwnership($financeCategory);

        $financeCategory->update($this->validated($request));

        return redirect()->route('admin.finance-categories.index')->with('success', 'Category saved.');
    }

    /**
     * Deleting a category keeps its history: the foreign key is nulled, not
     * cascaded, so the month's total never changes because a label was tidied
     * up.
     */
    public function destroy(FinanceCategory $financeCategory): RedirectResponse
    {
        $this->authorizeAction('delete');
        $this->authorizeOwnership($financeCategory);

        $financeCategory->delete();

        return redirect()->route('admin.finance-categories.index')
            ->with('success', 'Category deleted. Its transactions are kept as uncategorised.');
    }

    /**
     * Bring the account in line with the shipped catalogue.
     *
     * More than a top-up: a release can also move a keyword to a category that
     * did not exist before, or retire a bucket that has been split in two. The
     * message says which of the three actually happened, because "nothing to
     * do" and "twelve categories rewritten" deserve different reactions.
     */
    public function restoreDefaults(): RedirectResponse
    {
        $this->authorizeAction('create');

        $result = $this->finance->syncDefaults(Auth::user());

        $parts = array_filter([
            $result['created'] ? "added {$result['created']}" : null,
            $result['updated'] ? "refreshed {$result['updated']}" : null,
            $result['retired'] ? "switched off {$result['retired']}" : null,
        ]);

        return redirect()->route('admin.finance-categories.index')->with(
            'success',
            $parts === []
                ? 'Categories are already in step with the shipped list.'
                : 'Categories synced — ' . implode(', ', $parts) . '.'
        );
    }

    /** @return array<string, mixed> */
    private function validated(Request $request): array
    {
        return $request->validate([
            'kind' => ['required', 'string', 'in:' . implode(',', array_column(TransactionKind::cases(), 'value'))],
            'name' => ['required', 'string', 'max:60'],
            'icon' => ['nullable', 'string', 'max:16'],
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'keywords' => ['nullable', 'string', 'max:2000'],
            'monthly_limit' => ['nullable', 'integer', 'min:0', 'max:999999999999'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]) + ['is_active' => $request->boolean('is_active')];
    }

    private function authorizeAction(string $action): void
    {
        abort_unless(Auth::user()?->can("finance-categories.{$action}"), 403);
    }

    private function authorizeOwnership(FinanceCategory $category): void
    {
        abort_unless($category->user_id === Auth::id(), 403);
    }
}
