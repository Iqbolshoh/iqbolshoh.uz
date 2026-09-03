<?php

namespace App\Services;

use App\Enums\TransactionKind;
use App\Enums\TransactionSource;
use App\Models\FinanceCategory;
use App\Models\Transaction;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Str;

/**
 * The money half of the bot.
 *
 * Kept apart from TelegramBot because the two halves answer different
 * questions and are read by different people at different times — and because
 * a plan reminder must keep working on a day this file is being changed.
 *
 * The design rule throughout: one line of text is one recorded expense. Every
 * button here exists for the cases where that was not enough, never as the
 * normal path.
 */
class FinanceBot
{
    public function __construct(
        private readonly TelegramClient $client,
        private readonly FinanceService $finance,
        private readonly MoneyTextParser $parser,
    ) {}

    /**
     * Try to read a free-text line as money.
     *
     * Returns false when the text carries no amount at all, so the caller can
     * fall back to its own handling instead of this swallowing every message
     * the bot receives.
     */
    public function handleText(int $chatId, User $user, string $text): bool
    {
        $categories = $this->finance->categories($user);

        $parsed = $this->parser->parse($text, $categories);

        if ($parsed === null) {
            return false;
        }

        $result = $this->finance->record(
            user: $user,
            kind: $parsed['kind'],
            amount: $parsed['amount'],
            category: $parsed['category'],
            note: $parsed['note'],
            source: TransactionSource::Telegram,
        );

        $transaction = $result['transaction'];

        $lines = [$this->confirmation($transaction)];

        if ($result['warning'] !== null) {
            $lines[] = '';
            $lines[] = __('bot.fin.warning', [
                'category' => $result['warning']['category']->label(),
                'used' => $result['warning']['used'],
                'total' => Transaction::money($result['warning']['total']),
                'limit' => Transaction::money($result['warning']['limit']),
            ]);
        }

        $lines[] = '';
        $lines[] = __('bot.fin.today', ['amount' => Transaction::money($this->spentToday($user))]);

        // No category recognised: ask, rather than file it somewhere plausible.
        // The answer also teaches the parser the word that was not recognised.
        $keyboard = $parsed['category'] === null
            ? $this->categoryPicker($transaction, $categories->where('kind', $parsed['kind']))
            : $this->afterSaveKeyboard($transaction);

        if ($parsed['category'] === null) {
            $lines[] = '';
            $lines[] = __('bot.fin.ask_category');
        }

        $this->client->sendMessage($chatId, implode("\n", $lines), $keyboard);

        return true;
    }

    /**
     * A button on a saved transaction.
     *
     * @param  list<string>  $parts  "f", action, and whatever the action needs
     */
    public function handleCallback(int $chatId, int $messageId, User $user, array $parts): void
    {
        match ($parts[1] ?? '') {
            'cat' => $this->assignCategory($chatId, $messageId, $user, (int) ($parts[2] ?? 0), (int) ($parts[3] ?? 0)),
            'undo' => $this->undo($chatId, $messageId, $user, (int) ($parts[2] ?? 0)),
            'menu' => $this->sendMenu($chatId, $user, $messageId),
            'month' => $this->sendMonth($chatId, $user, $messageId),
            default => null,
        };
    }

    /** The money screen: today, the month, and how the budget is holding. */
    public function sendMenu(int $chatId, User $user, ?int $editMessageId = null): void
    {
        $stats = new FinanceStats($user->id, $user->timezone);
        $today = $stats->today();
        $summary = $stats->summary($today->startOfMonth(), $today->endOfMonth());
        $budget = $stats->budgetStatus($today);

        $lines = [
            __('bot.fin.title'),
            '',
            __('bot.fin.today', ['amount' => Transaction::money($this->spentToday($user))]),
            __('bot.fin.month_so_far', ['amount' => Transaction::money($summary['expense'])]),
            __('bot.fin.income', ['amount' => Transaction::money($summary['income'])]),
            '',
            $this->budgetLine($budget),
        ];

        $this->deliver($chatId, $editMessageId, implode("\n", $lines), TelegramClient::keyboard([
            [
                TelegramClient::button(__('bot.btn.month'), 'f:month'),
                TelegramClient::button(__('bot.btn.today'), 'nav:today'),
            ],
        ]));
    }

    /** The month in detail: where it went, biggest first. */
    public function sendMonth(int $chatId, User $user, ?int $editMessageId = null): void
    {
        $stats = new FinanceStats($user->id, $user->timezone);
        $today = $stats->today();
        $start = $today->startOfMonth();
        $end = $today->endOfMonth();

        $summary = $stats->summary($start, $end);

        if ($summary['count'] === 0) {
            $this->deliver($chatId, $editMessageId, __('bot.fin.empty_month'), TelegramClient::keyboard([
                [TelegramClient::button(__('bot.btn.back'), 'f:menu')],
            ]));

            return;
        }

        $lines = [
            __('bot.fin.month_title', ['month' => $start->translatedFormat('F Y')]),
            '',
            __('bot.fin.expense', ['amount' => Transaction::money($summary['expense'])]),
            __('bot.fin.income', ['amount' => Transaction::money($summary['income'])]),
            __('bot.fin.balance', ['amount' => Transaction::money($summary['balance'])]),
            '',
            __('bot.fin.by_category'),
        ];

        foreach ($stats->byCategory($start, $end)->take(8) as $row) {
            $lines[] = sprintf(
                '%s — %s (%s%%)',
                $row['category']?->label() ?? '—',
                Transaction::money($row['total']),
                $row['share']
            );
        }

        $lines[] = '';
        $lines[] = $this->budgetLine($stats->budgetStatus($today));

        $this->deliver($chatId, $editMessageId, implode("\n", $lines), TelegramClient::keyboard([
            [TelegramClient::button(__('bot.btn.back'), 'f:menu')],
        ]));
    }

    /** The evening nudge, sent by the scheduler. */
    public function sendPrompt(int $chatId, User $user): void
    {
        $this->client->sendMessage($chatId, __('bot.fin.prompt'), TelegramClient::keyboard([
            [TelegramClient::button(__('bot.btn.money'), 'f:menu')],
        ]));
    }

    /**
     * File a transaction the parser could not place, and remember the word
     * that confused it.
     */
    private function assignCategory(int $chatId, int $messageId, User $user, int $transactionId, int $categoryId): void
    {
        $transaction = Transaction::query()
            ->where('user_id', $user->id)
            ->find($transactionId);

        $category = FinanceCategory::query()
            ->where('user_id', $user->id)
            ->find($categoryId);

        if ($transaction === null || $category === null) {
            return;
        }

        $transaction->update(['category_id' => $category->id, 'kind' => $category->kind->value]);

        $learned = $this->learn($category, $transaction->note);

        $lines = [$this->confirmation($transaction->fresh('category'))];

        if ($learned !== null) {
            $lines[] = '';
            $lines[] = __('bot.fin.learned', ['word' => $learned, 'category' => $category->label()]);
        }

        $lines[] = '';
        $lines[] = __('bot.fin.today', ['amount' => Transaction::money($this->spentToday($user))]);

        $this->client->editMessage(
            $chatId,
            $messageId,
            implode("\n", $lines),
            $this->afterSaveKeyboard($transaction)
        );
    }

    /**
     * Teach the category the word the owner just corrected.
     *
     * Only a single plain word is learned: a whole phrase would match almost
     * nothing next time, and a word already known is not added twice. The note
     * is cleared afterwards, because it has become the category.
     */
    private function learn(FinanceCategory $category, ?string $note): ?string
    {
        $word = Str::of((string) $note)->lower()->trim()->toString();

        if ($word === '' || Str::contains($word, ' ') || mb_strlen($word) < 3 || mb_strlen($word) > 24) {
            return null;
        }

        if (in_array($word, $category->matchWords(), true)) {
            return null;
        }

        $category->update([
            'keywords' => trim($category->keywords . ',' . $word, ','),
        ]);

        return $word;
    }

    /** Remove a row the bot itself added. */
    private function undo(int $chatId, int $messageId, User $user, int $transactionId): void
    {
        $transaction = Transaction::query()
            ->where('user_id', $user->id)
            ->where('source', TransactionSource::Telegram->value)
            ->with('category')
            ->find($transactionId);

        if ($transaction === null) {
            $this->client->editMessage($chatId, $messageId, __('bot.fin.nothing_to_undo'));

            return;
        }

        $label = $transaction->category?->label() ?? __('finance.kind.' . $transaction->kind->value);
        $amount = Transaction::money($transaction->amount);

        $transaction->delete();

        $this->client->editMessage($chatId, $messageId, implode("\n", [
            __('bot.fin.undone', ['amount' => $amount, 'category' => $label]),
            '',
            __('bot.fin.today', ['amount' => Transaction::money($this->spentToday($user))]),
        ]));
    }

    private function confirmation(Transaction $transaction): string
    {
        $amount = Transaction::money($transaction->amount);

        return $transaction->category === null
            ? __('bot.fin.saved_uncategorised', ['amount' => $amount])
            : __('bot.fin.saved', ['amount' => $amount, 'category' => $transaction->category->label()]);
    }

    /** @param  \Illuminate\Support\Collection<int, FinanceCategory>  $categories */
    private function categoryPicker(Transaction $transaction, $categories): array
    {
        $rows = [];
        $buttons = [];

        foreach ($categories as $category) {
            $buttons[] = TelegramClient::button($category->label(), "f:cat:{$transaction->id}:{$category->id}");

            if (count($buttons) === 2) {
                $rows[] = $buttons;
                $buttons = [];
            }
        }

        if ($buttons !== []) {
            $rows[] = $buttons;
        }

        $rows[] = [TelegramClient::button(__('bot.btn.undo'), "f:undo:{$transaction->id}")];

        return TelegramClient::keyboard($rows);
    }

    private function afterSaveKeyboard(Transaction $transaction): array
    {
        return TelegramClient::keyboard([
            [
                TelegramClient::button(__('bot.btn.undo'), "f:undo:{$transaction->id}"),
                TelegramClient::button(__('bot.btn.money'), 'f:menu'),
            ],
        ]);
    }

    /** @param  array<string, mixed>  $budget */
    private function budgetLine(array $budget): string
    {
        if ($budget['budget'] === null) {
            return __('bot.fin.no_budget');
        }

        return $budget['left'] >= 0
            ? __('bot.fin.left', [
                'amount' => Transaction::money($budget['left']),
                'budget' => Transaction::money($budget['budget']),
            ])
            : __('bot.fin.over', [
                'amount' => Transaction::money(abs($budget['left'])),
                'budget' => Transaction::money($budget['budget']),
            ]);
    }

    private function spentToday(User $user): int
    {
        $today = CarbonImmutable::today($user->timezone);

        return (int) Transaction::query()
            ->where('user_id', $user->id)
            ->ofKind(TransactionKind::Expense)
            ->between($today, $today)
            ->sum('amount');
    }

    /** @param  array<string, mixed>  $keyboard */
    private function deliver(int $chatId, ?int $messageId, string $text, array $keyboard): void
    {
        $messageId === null
            ? $this->client->sendMessage($chatId, $text, $keyboard)
            : $this->client->editMessage($chatId, $messageId, $text, $keyboard);
    }
}
