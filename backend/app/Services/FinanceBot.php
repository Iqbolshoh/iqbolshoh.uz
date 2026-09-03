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
            'skip' => $this->skipCategory($chatId, $messageId, $user, (int) ($parts[2] ?? 0)),
            'undo' => $this->undo($chatId, $messageId, $user, (int) ($parts[2] ?? 0)),
            'menu' => $this->sendMenu($chatId, $user, $messageId),
            'week' => $this->sendPeriod($chatId, $user, 'week', $messageId),
            'month' => $this->sendPeriod($chatId, $user, 'month', $messageId),
            'recent' => $this->sendRecent($chatId, $user, $messageId),
            default => null,
        };
    }

    /** The money screen: today, the month, and how the budget is holding. */
    public function sendMenu(int $chatId, User $user, ?int $editMessageId = null): void
    {
        $stats = new FinanceStats($user->id, $user->timezone);
        $today = $stats->today();
        $month = $stats->summary($today->startOfMonth(), $today->endOfMonth());
        $week = $stats->summary($today->startOfWeek(), $today->endOfWeek());
        $budget = $stats->budgetStatus($today);

        $lines = [
            __('bot.fin.title'),
            '',
            __('bot.fin.today', ['amount' => Transaction::money($this->spentToday($user))]),
            __('bot.fin.week_so_far', ['amount' => Transaction::money($week['expense'])]),
            __('bot.fin.month_so_far', ['amount' => Transaction::money($month['expense'])]),
            __('bot.fin.income', ['amount' => Transaction::money($month['income'])]),
            '',
            $this->budgetLine($budget),
        ];

        // Only worth saying once there is enough of the month behind it for the
        // projection to mean anything.
        if ($budget['budget'] !== null && $today->day >= 5) {
            $lines[] = __('bot.fin.pace', ['amount' => Transaction::money((int) $budget['projected'])]);
        }

        $this->deliver($chatId, $editMessageId, implode("\n", $lines), TelegramClient::keyboard(
            $this->menuRows($user, $month['count'])
        ));
    }

    /**
     * The money screen's buttons, which depend on what there is to look at.
     *
     * A button that leads to an empty screen is worse than a missing one: it
     * reads as a broken feature rather than as nothing having happened yet. So
     * "recent" appears once anything has been recorded, and "undo" only while
     * there is a row of the bot's own to take back.
     *
     * @return list<list<array<string, string>>>
     */
    private function menuRows(User $user, int $monthCount): array
    {
        $rows = [[
            TelegramClient::button(__('bot.btn.week'), 'f:week'),
            TelegramClient::button(__('bot.btn.month'), 'f:month'),
        ]];

        $second = [];

        if ($this->hasAny($user)) {
            $second[] = TelegramClient::button(__('bot.btn.recent'), 'f:recent');
        }

        if (($last = $this->finance->lastFrom($user, TransactionSource::Telegram)) !== null) {
            $second[] = TelegramClient::button(__('bot.btn.undo'), "f:undo:{$last->id}");
        }

        if ($second !== []) {
            $rows[] = $second;
        }

        $rows[] = [
            TelegramClient::button(__('bot.btn.today'), 'nav:today'),
            TelegramClient::button(__('bot.btn.home'), 'nav:menu'),
        ];

        return $rows;
    }

    /**
     * A period in detail: where it went, biggest first.
     *
     * The week and the month are one screen because they answer the same
     * question at two zoom levels; keeping them apart would have meant two
     * copies of the breakdown drifting away from each other.
     */
    public function sendPeriod(int $chatId, User $user, string $period, ?int $editMessageId = null): void
    {
        $stats = new FinanceStats($user->id, $user->timezone);
        $today = $stats->today();

        [$start, $end, $title, $empty, $other] = $period === 'week'
            ? [$today->startOfWeek(), $today->endOfWeek(), __('bot.fin.week_title'), __('bot.fin.empty_week'), 'month']
            : [
                $today->startOfMonth(),
                $today->endOfMonth(),
                __('bot.fin.month_title', ['month' => $today->translatedFormat('F Y')]),
                __('bot.fin.empty_month'),
                'week',
            ];

        $summary = $stats->summary($start, $end);

        if ($summary['count'] === 0) {
            $this->deliver($chatId, $editMessageId, $title . "\n\n" . $empty, TelegramClient::keyboard([
                [
                    TelegramClient::button(__("bot.btn.{$other}"), "f:{$other}"),
                    TelegramClient::button(__('bot.btn.back'), 'f:menu'),
                ],
            ]));

            return;
        }

        $lines = [
            $title,
            '',
            __('bot.fin.expense', ['amount' => Transaction::money($summary['expense'])]),
            __('bot.fin.income', ['amount' => Transaction::money($summary['income'])]),
            __('bot.fin.balance', ['amount' => Transaction::money($summary['balance'])]),
            '<i>' . __('bot.fin.entries', ['count' => $summary['count']]) . '</i>',
            '',
            __('bot.fin.by_category'),
        ];

        foreach ($stats->byCategory($start, $end)->take(8) as $row) {
            $lines[] = sprintf(
                '<code>%s</code> %s · <b>%s</b>',
                $this->bar((float) $row['share']),
                $row['category']?->label() ?? '—',
                Transaction::money($row['total'])
            );
        }

        if ($period === 'month') {
            $lines[] = '';
            $lines[] = $this->budgetLine($stats->budgetStatus($today));
        }

        $this->deliver($chatId, $editMessageId, implode("\n", $lines), TelegramClient::keyboard([
            [
                TelegramClient::button(__("bot.btn.{$other}"), "f:{$other}"),
                TelegramClient::button(__('bot.btn.recent'), 'f:recent'),
            ],
            [
                TelegramClient::button(__('bot.btn.back'), 'f:menu'),
                TelegramClient::button(__('bot.btn.home'), 'nav:menu'),
            ],
        ]));
    }

    /**
     * The last handful of rows, newest first.
     *
     * Exists because "undo" only ever reaches the most recent one: without a
     * list, a mistake two rows back can only be found in the panel.
     */
    public function sendRecent(int $chatId, User $user, ?int $editMessageId = null): void
    {
        $rows = Transaction::query()
            ->where('user_id', $user->id)
            ->with('category')
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        $lines = [__('bot.fin.recent_title'), ''];

        if ($rows->isEmpty()) {
            $lines[] = __('bot.fin.empty_recent');
        }

        foreach ($rows as $transaction) {
            $lines[] = sprintf(
                '<code>%s</code>  %s · %s',
                $transaction->date->format('d.m'),
                $transaction->formattedAmount(),
                $transaction->category?->label() ?? '—'
            );
        }

        $buttons = [];

        if (($last = $this->finance->lastFrom($user, TransactionSource::Telegram)) !== null) {
            $buttons[] = TelegramClient::button(__('bot.btn.undo'), "f:undo:{$last->id}");
        }

        $buttons[] = TelegramClient::button(__('bot.btn.back'), 'f:menu');

        $this->deliver($chatId, $editMessageId, implode("\n", $lines), TelegramClient::keyboard([
            $buttons,
            [TelegramClient::button(__('bot.btn.home'), 'nav:menu')],
        ]));
    }

    /**
     * A share as a bar, so eight categories can be read as shapes instead of
     * as eight numbers compared to each other. Eight cells: wide enough to be
     * a shape, narrow enough that the line does not wrap on a phone.
     */
    private function bar(float $share): string
    {
        $filled = max(1, min(8, (int) round($share / 100 * 8)));

        return str_repeat('▓', $filled) . str_repeat('░', 8 - $filled);
    }

    private function hasAny(User $user): bool
    {
        return Transaction::query()->where('user_id', $user->id)->exists();
    }

    /** The evening nudge, sent by the scheduler. */
    public function sendPrompt(int $chatId, User $user): void
    {
        $this->client->sendMessage($chatId, __('bot.fin.prompt'), TelegramClient::keyboard([[
            TelegramClient::button(__('bot.btn.money'), 'f:menu'),
            TelegramClient::button(__('bot.btn.recent'), 'f:recent'),
        ]]));
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

    /**
     * Leave the row where it is, without a category.
     *
     * The money is still spent, so the amount stays; only the question goes
     * away. The panel's ledger shows it under "—" and it can be categorised
     * there whenever the owner feels like it.
     */
    private function skipCategory(int $chatId, int $messageId, User $user, int $transactionId): void
    {
        $transaction = Transaction::query()
            ->where('user_id', $user->id)
            ->with('category')
            ->find($transactionId);

        if ($transaction === null) {
            $this->client->editMessage($chatId, $messageId, __('bot.fin.nothing_to_undo'));

            return;
        }

        $this->client->editMessage($chatId, $messageId, implode("\n", [
            $this->confirmation($transaction),
            '',
            __('bot.fin.today', ['amount' => Transaction::money($this->spentToday($user))]),
        ]), $this->afterSaveKeyboard($transaction));
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
        ]), TelegramClient::keyboard([[
            TelegramClient::button(__('bot.btn.money'), 'f:menu'),
            TelegramClient::button(__('bot.btn.home'), 'nav:menu'),
        ]]));
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

        // Two ways out, because neither is the same answer: skip keeps the
        // amount and leaves it uncategorised, undo says the row was a mistake.
        $rows[] = [
            TelegramClient::button(__('bot.btn.skip'), "f:skip:{$transaction->id}"),
            TelegramClient::button(__('bot.btn.undo'), "f:undo:{$transaction->id}"),
        ];

        return TelegramClient::keyboard($rows);
    }

    private function afterSaveKeyboard(Transaction $transaction): array
    {
        return TelegramClient::keyboard([
            [
                TelegramClient::button(__('bot.btn.undo'), "f:undo:{$transaction->id}"),
                TelegramClient::button(__('bot.btn.recent'), 'f:recent'),
            ],
            [
                TelegramClient::button(__('bot.btn.money'), 'f:menu'),
                TelegramClient::button(__('bot.btn.home'), 'nav:menu'),
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
