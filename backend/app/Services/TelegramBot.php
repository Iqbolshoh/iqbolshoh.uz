<?php

namespace App\Services;

use App\Enums\FailReason;
use App\Enums\InterruptionType;
use App\Enums\PlanStatus;
use App\Enums\PostponeReason;
use App\Enums\TransactionKind;
use App\Models\Interruption;
use App\Models\Plan;
use App\Models\TelegramAccount;
use App\Models\Transaction;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

/**
 * What the bot says and does.
 *
 * Callback data is kept deliberately terse — `p:41:pp:30` — because Telegram
 * caps it at 64 bytes and a truncated payload is a button that silently does
 * nothing.
 */
class TelegramBot
{
    public function __construct(
        private readonly TelegramClient $client,
        private readonly PlanService $plans,
        private readonly FinanceBot $finance,
    ) {}

    /** @param  array<string, mixed>  $message */
    public function handleMessage(array $message): void
    {
        $chatId = (int) ($message['chat']['id'] ?? 0);
        $text = trim((string) ($message['text'] ?? ''));

        $account = $this->account($chatId);

        if ($account === null) {
            // An unlinked chat is answered in whatever language its client
            // reports, since there is no stored preference to honour yet.
            $this->useLocale($message['from']['language_code'] ?? null);
            $this->client->sendMessage($chatId, __('bot.private', ['id' => $chatId]));

            return;
        }

        $this->useLocale($account->locale);

        $command = Str::of($text)->lower()->before(' ')->trim()->toString();

        match ($command) {
            '/start', '/menu' => $this->sendWelcome($chatId, $account->user),
            '/today' => $this->sendDay($chatId, $account->user, CarbonImmutable::today($account->user->timezone)),
            '/tomorrow' => $this->sendDay($chatId, $account->user, CarbonImmutable::tomorrow($account->user->timezone)),
            '/status' => $this->sendInterruptionMenu($chatId),
            '/stats' => $this->sendStats($chatId, $account->user),
            '/money', '/pul' => $this->finance->sendMenu($chatId, $account->user),
            '/language', '/til' => $this->sendLanguageMenu($chatId),
            default => $this->handleFreeText($chatId, $account->user, $text),
        };
    }

    /**
     * Anything that is not a command.
     *
     * Money comes first because that is what most messages here are: a line
     * like "ovqat 25000" must be one thing to type and nothing to confirm. Only
     * when no amount can be found does the bot fall back to showing the menu,
     * so a greeting still gets a useful answer instead of an error.
     */
    private function handleFreeText(int $chatId, User $user, string $text): void
    {
        if ($text !== '' && $this->finance->handleText($chatId, $user, $text)) {
            return;
        }

        $this->sendWelcome($chatId, $user);
    }

    /**
     * A button press. The caller has already acknowledged it, so everything
     * here is free to take its time.
     *
     * @param  array<string, mixed>  $query
     */
    public function handleCallback(array $query): void
    {
        $chatId = (int) ($query['message']['chat']['id'] ?? 0);
        $messageId = (int) ($query['message']['message_id'] ?? 0);
        $data = (string) ($query['data'] ?? '');

        $account = $this->account($chatId);

        if ($account === null) {
            return;
        }

        $this->useLocale($account->locale);

        $parts = explode(':', $data);

        match ($parts[0] ?? '') {
            'p' => $this->handlePlanAction($chatId, $messageId, $account->user, $parts),
            'i' => $this->handleInterruptionAction($chatId, $messageId, $account->user, $parts),
            'nav' => $this->handleNavigation($chatId, $messageId, $account->user, $parts),
            'f' => $this->finance->handleCallback($chatId, $messageId, $account->user, $parts),
            'lang' => $this->setLanguage($chatId, $messageId, $account, $parts[1] ?? ''),
            default => null,
        };
    }

    /**
     * Point the translator at the right language for this one update.
     *
     * Set on every update rather than once at boot: the queue worker is a long
     * running process, so a locale left over from the previous chat would
     * answer the next one in the wrong language — and on a single-owner bot
     * that bug is invisible until someone else writes in.
     */
    private function useLocale(?string $locale): void
    {
        App::setLocale($this->resolveLocale($locale));
    }

    /** Telegram sends "ru-RU" and knows Tajik as "tg"; this bot's files say "tj". */
    private function resolveLocale(?string $locale): string
    {
        $short = Str::of((string) $locale)->lower()->before('-')->toString();

        return match ($short) {
            'uz', 'ru', 'en' => $short,
            'tj', 'tg' => 'tj',
            default => config('app.locale'),
        };
    }

    private function sendLanguageMenu(int $chatId, ?int $messageId = null): void
    {
        $keyboard = TelegramClient::keyboard([
            [
                TelegramClient::button("🇺🇿 O'zbekcha", 'lang:uz'),
                TelegramClient::button('🇷🇺 Русский', 'lang:ru'),
            ],
            [
                TelegramClient::button('🇬🇧 English', 'lang:en'),
                TelegramClient::button('🇹🇯 Тоҷикӣ', 'lang:tj'),
            ],
        ]);

        $messageId === null
            ? $this->client->sendMessage($chatId, __('bot.lang.ask'), $keyboard)
            : $this->client->editMessage($chatId, $messageId, __('bot.lang.ask'), $keyboard);
    }

    private function setLanguage(int $chatId, int $messageId, TelegramAccount $account, string $locale): void
    {
        $resolved = $this->resolveLocale($locale);

        $account->update(['locale' => $resolved]);
        App::setLocale($resolved);

        $this->client->editMessage($chatId, $messageId, __('bot.lang.set'));
        $this->sendWelcome($chatId, $account->user);
    }

    /** The card a reminder sends, and the one every action edits in place. */
    public function planCard(Plan $plan): string
    {
        $time = Str::substr((string) $plan->start_time, 0, 5);
        $lines = [
            '⏰ <b>' . e($plan->title) . '</b>',
            '',
            "🕒 {$time} · " . Plan::humanMinutes($plan->planned_minutes),
        ];

        if ($plan->goal) {
            $lines[] = '🎯 ' . e($plan->goal->title);
        }

        if ($plan->postpone_count > 0) {
            $lines[] = __('bot.plan.pushed', ['count' => $plan->postpone_count]);
        }

        $lines[] = '';
        $lines[] = __('bot.plan.status', ['status' => $this->statusLabel($plan->status)]);

        return implode("\n", $lines);
    }

    /** @return array<string, mixed> */
    public function planKeyboard(Plan $plan): array
    {
        if ($plan->status->isClosed()) {
            return TelegramClient::keyboard([
                [TelegramClient::button(__('bot.btn.today'), 'nav:today')],
            ]);
        }

        return TelegramClient::keyboard([
            [
                TelegramClient::button(__('bot.btn.done'), "p:{$plan->id}:done"),
                TelegramClient::button(__('bot.btn.not_done'), "p:{$plan->id}:fail"),
            ],
            [
                TelegramClient::button(__('bot.btn.minutes', ['count' => 10]), "p:{$plan->id}:pp:10"),
                TelegramClient::button(__('bot.btn.minutes', ['count' => 30]), "p:{$plan->id}:pp:30"),
            ],
            [
                TelegramClient::button(__('bot.btn.later'), "p:{$plan->id}:later"),
                TelegramClient::button(__('bot.btn.today'), 'nav:today'),
            ],
        ]);
    }

    private function sendWelcome(int $chatId, User $user): void
    {
        $today = CarbonImmutable::today($user->timezone);
        $plans = $this->plansFor($user, $today);
        $done = $plans->where('status', PlanStatus::Completed)->count();

        $spentToday = (int) Transaction::query()
            ->where('user_id', $user->id)
            ->ofKind(TransactionKind::Expense)
            ->between($today, $today)
            ->sum('amount');

        $text = implode("\n", [
            __('bot.welcome.title'),
            '',
            __('bot.welcome.plans', ['total' => $plans->count(), 'done' => $done]),
            __('bot.welcome.spent', ['amount' => Transaction::money($spentToday)]),
            '',
            __('bot.welcome.ask'),
            '',
            '<i>' . __('bot.welcome.hint') . '</i>',
        ]);

        $this->client->sendMessage($chatId, $text, TelegramClient::keyboard([
            [
                TelegramClient::button(__('bot.btn.today'), 'nav:today'),
                TelegramClient::button(__('bot.btn.tomorrow'), 'nav:tomorrow'),
            ],
            [
                TelegramClient::button(__('bot.btn.money'), 'f:menu'),
                TelegramClient::button(__('bot.btn.stats'), 'nav:stats'),
            ],
            [
                TelegramClient::button(__('bot.btn.status'), 'nav:status'),
                TelegramClient::button(__('bot.btn.language'), 'nav:language'),
            ],
        ]));
    }

    private function sendDay(int $chatId, User $user, CarbonImmutable $date, ?int $editMessageId = null): void
    {
        $plans = $this->plansFor($user, $date);

        if ($plans->isEmpty()) {
            $text = '📋 <b>' . $date->translatedFormat('l, j F') . '</b>' . "\n\n" . __('bot.day.empty');
            $keyboard = TelegramClient::keyboard([[TelegramClient::button(__('bot.btn.tomorrow'), 'nav:tomorrow')]]);

            $editMessageId
                ? $this->client->editMessage($chatId, $editMessageId, $text, $keyboard)
                : $this->client->sendMessage($chatId, $text, $keyboard);

            return;
        }

        $lines = ['📋 <b>' . $date->translatedFormat('l, j F') . '</b>', ''];
        $rows = [];

        foreach ($plans as $plan) {
            $time = Str::substr((string) $plan->start_time, 0, 5);
            $lines[] = $this->statusEmoji($plan->status) . " <code>{$time}</code> " . e($plan->title);

            if (! $plan->status->isClosed()) {
                $rows[] = [TelegramClient::button("{$time} · " . Str::limit($plan->title, 24), "p:{$plan->id}:open")];
            }
        }

        $done = $plans->where('status', PlanStatus::Completed)->count();
        $settled = $plans->reject(fn (Plan $plan): bool => ! $plan->status->isClosed())->count();

        $lines[] = '';
        $lines[] = $settled > 0
            ? __('bot.day.settled', [
                'done' => $done,
                'settled' => $settled,
                'rate' => round($done / max(1, $settled) * 100),
            ])
            : __('bot.day.nothing_settled');

        $rows[] = [
            TelegramClient::button(__('bot.btn.refresh'), 'nav:today'),
            TelegramClient::button(__('bot.btn.money'), 'f:menu'),
        ];

        $text = implode("\n", $lines);
        $keyboard = TelegramClient::keyboard($rows);

        $editMessageId
            ? $this->client->editMessage($chatId, $editMessageId, $text, $keyboard)
            : $this->client->sendMessage($chatId, $text, $keyboard);
    }

    private function sendStats(int $chatId, User $user, ?int $editMessageId = null): void
    {
        $stats = new PlanStats($user->id);
        $today = CarbonImmutable::today($user->timezone);

        $week = $stats->summary($today->startOfWeek(), $today->endOfWeek());
        $month = $stats->summary($today->startOfMonth(), $today->endOfMonth());

        $text = implode("\n", [
            __('bot.stats.title'),
            '',
            __('bot.stats.week'),
            __('bot.stats.plans', ['total' => $week['total'], 'completed' => $week['completed']]),
            __('bot.stats.rate', ['raw' => $week['raw_rate'], 'true' => $week['true_rate']]),
            '',
            __('bot.stats.month'),
            __('bot.stats.plans', ['total' => $month['total'], 'completed' => $month['completed']]),
            __('bot.stats.rate', ['raw' => $month['raw_rate'], 'true' => $month['true_rate']]),
            '',
            __('bot.stats.time', [
                'planned' => Plan::humanMinutes($month['planned_minutes']),
                'actual' => Plan::humanMinutes($month['actual_minutes']),
            ]),
        ]);

        $keyboard = TelegramClient::keyboard([[TelegramClient::button(__('bot.btn.today'), 'nav:today')]]);

        $editMessageId
            ? $this->client->editMessage($chatId, $editMessageId, $text, $keyboard)
            : $this->client->sendMessage($chatId, $text, $keyboard);
    }

    private function sendInterruptionMenu(int $chatId, ?int $editMessageId = null): void
    {
        $text = __('bot.interrupt.title') . "\n\n" . __('bot.interrupt.hint');

        $rows = [];
        $buttons = [];

        foreach (InterruptionType::cases() as $type) {
            $buttons[] = TelegramClient::button($type->emoji() . ' ' . $this->interruptLabel($type), "i:start:{$type->value}");

            if (count($buttons) === 2) {
                $rows[] = $buttons;
                $buttons = [];
            }
        }

        if ($buttons !== []) {
            $rows[] = $buttons;
        }

        $rows[] = [TelegramClient::button(__('bot.btn.today'), 'nav:today')];

        $keyboard = TelegramClient::keyboard($rows);

        $editMessageId
            ? $this->client->editMessage($chatId, $editMessageId, $text, $keyboard)
            : $this->client->sendMessage($chatId, $text, $keyboard);
    }

    /** @param  list<string>  $parts */
    private function handlePlanAction(int $chatId, int $messageId, User $user, array $parts): void
    {
        $plan = Plan::query()->where('user_id', $user->id)->find((int) ($parts[1] ?? 0));

        if ($plan === null) {
            $this->client->editMessage($chatId, $messageId, __('bot.plan.gone'));

            return;
        }

        match ($parts[2] ?? '') {
            'open' => null,
            'done' => $this->plans->complete($plan),
            'fail' => isset($parts[3])
                ? $this->plans->fail($plan, FailReason::tryFrom($parts[3]) ?? FailReason::Other)
                : $this->askFailReason($chatId, $messageId, $plan),
            'pp' => $this->plans->postpone($plan, (int) ($parts[3] ?? 30)),
            'later' => $this->askPostponeWindow($chatId, $messageId, $plan),
            default => null,
        };

        // The two "ask" branches have already replaced the card with a question.
        if (in_array($parts[2] ?? '', ['later'], true) || (($parts[2] ?? '') === 'fail' && ! isset($parts[3]))) {
            return;
        }

        $plan->refresh();
        $this->client->editMessage($chatId, $messageId, $this->planCard($plan), $this->planKeyboard($plan));
    }

    private function askFailReason(int $chatId, int $messageId, Plan $plan): void
    {
        $rows = [];
        $buttons = [];

        foreach (FailReason::cases() as $reason) {
            $buttons[] = TelegramClient::button($reason->emoji() . ' ' . $this->failLabel($reason), "p:{$plan->id}:fail:{$reason->value}");

            if (count($buttons) === 2) {
                $rows[] = $buttons;
                $buttons = [];
            }
        }

        if ($buttons !== []) {
            $rows[] = $buttons;
        }

        $rows[] = [TelegramClient::button(__('bot.btn.back'), "p:{$plan->id}:open")];

        $this->client->editMessage(
            $chatId,
            $messageId,
            '❌ <b>' . e($plan->title) . '</b>' . "\n\n" . __('bot.plan.fail_question'),
            TelegramClient::keyboard($rows)
        );
    }

    private function askPostponeWindow(int $chatId, int $messageId, Plan $plan): void
    {
        $this->client->editMessage(
            $chatId,
            $messageId,
            '⏭ <b>' . e($plan->title) . '</b>' . "\n\n" . __('bot.plan.later_question'),
            TelegramClient::keyboard([
                [
                    TelegramClient::button(__('bot.btn.minutes', ['count' => 30]), "p:{$plan->id}:pp:30"),
                    TelegramClient::button(__('bot.btn.hour'), "p:{$plan->id}:pp:60"),
                ],
                [
                    TelegramClient::button(__('bot.btn.evening'), "p:{$plan->id}:pp:" . $this->minutesUntilEvening($plan)),
                    TelegramClient::button(__('bot.btn.tomorrow_short'), "p:{$plan->id}:pp:1440"),
                ],
                [TelegramClient::button(__('bot.btn.back'), "p:{$plan->id}:open")],
            ])
        );
    }

    /** @param  list<string>  $parts */
    private function handleInterruptionAction(int $chatId, int $messageId, User $user, array $parts): void
    {
        if (($parts[1] ?? '') === 'start') {
            $type = InterruptionType::tryFrom($parts[2] ?? '') ?? InterruptionType::Other;

            $this->askInterruptionLength($chatId, $messageId, $type);

            return;
        }

        if (($parts[1] ?? '') === 'for') {
            $type = InterruptionType::tryFrom($parts[2] ?? '') ?? InterruptionType::Other;
            $minutes = (int) ($parts[3] ?? 60);

            $this->beginInterruption($chatId, $messageId, $user, $type, $minutes);

            return;
        }

        if (($parts[1] ?? '') === 'end') {
            $interruption = Interruption::query()->where('user_id', $user->id)->find((int) ($parts[2] ?? 0));

            if ($interruption !== null) {
                $interruption->update(['ended_at' => now()]);
            }

            $this->sendDay($chatId, $user, CarbonImmutable::today($user->timezone), $messageId);
        }
    }

    private function askInterruptionLength(int $chatId, int $messageId, InterruptionType $type): void
    {
        $this->client->editMessage(
            $chatId,
            $messageId,
            $type->emoji() . ' <b>' . $this->interruptLabel($type) . '</b>' . "\n\n" . __('bot.interrupt.how_long'),
            TelegramClient::keyboard([
                [
                    TelegramClient::button(__('bot.btn.minutes', ['count' => 30]), "i:for:{$type->value}:30"),
                    TelegramClient::button(__('bot.btn.hour'), "i:for:{$type->value}:60"),
                ],
                [
                    TelegramClient::button(__('bot.btn.minutes', ['count' => 120]), "i:for:{$type->value}:120"),
                    TelegramClient::button(__('bot.btn.rest_of_day'), "i:for:{$type->value}:600"),
                ],
                [TelegramClient::button(__('bot.btn.back'), 'nav:status')],
            ])
        );
    }

    private function beginInterruption(
        int $chatId,
        int $messageId,
        User $user,
        InterruptionType $type,
        int $minutes,
    ): void {
        $startedAt = CarbonImmutable::now($user->timezone);

        $interruption = Interruption::query()->create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $type->label(),
            'started_at' => $startedAt,
            'ends_at' => $startedAt->addMinutes($minutes),
            'duration_minutes' => $minutes,
        ]);

        $affected = 0;

        // An emergency never moves anything on its own — the person decides.
        if ($type->movesPlansAutomatically()) {
            $plans = Plan::query()
                ->where('user_id', $user->id)
                ->open()
                ->forDate($startedAt)
                ->get()
                ->filter(fn (Plan $plan): bool => $plan->startsAt()->lessThan($startedAt->addMinutes($minutes)));

            foreach ($plans as $plan) {
                $this->plans->postpone($plan, $minutes, PostponeReason::Interruption, $interruption->id);
                $affected++;
            }

            $interruption->update(['affected_plans' => $affected]);
        }

        $text = $type->emoji() . ' <b>' . $this->interruptLabel($type) . '</b>' . "\n\n"
            . __('bot.interrupt.until', ['time' => $startedAt->addMinutes($minutes)->format('H:i')]) . "\n\n"
            . ($type->movesPlansAutomatically()
                ? ($affected > 0 ? __('bot.interrupt.moved', ['count' => $affected]) : __('bot.interrupt.nothing_moved'))
                : __('bot.interrupt.untouched'));

        $this->client->editMessage($chatId, $messageId, $text, TelegramClient::keyboard([
            [TelegramClient::button(__('bot.btn.free_again'), "i:end:{$interruption->id}")],
            [TelegramClient::button(__('bot.btn.today'), 'nav:today')],
        ]));
    }

    /** @param  list<string>  $parts */
    private function handleNavigation(int $chatId, int $messageId, User $user, array $parts): void
    {
        $today = CarbonImmutable::today($user->timezone);

        match ($parts[1] ?? '') {
            'today' => $this->sendDay($chatId, $user, $today, $messageId),
            'tomorrow' => $this->sendDay($chatId, $user, $today->addDay(), $messageId),
            'stats' => $this->sendStats($chatId, $user, $messageId),
            'status' => $this->sendInterruptionMenu($chatId, $messageId),
            'language' => $this->sendLanguageMenu($chatId, $messageId),
            default => null,
        };
    }

    /**
     * Enum labels, in the language of the chat.
     *
     * The enums keep their English `label()` for the admin panel, which is
     * English only; these read the same case through the bot's translation
     * files instead of duplicating the words in two places.
     */
    private function statusLabel(PlanStatus $status): string
    {
        return __('bot.plan_status.' . $status->value);
    }

    private function failLabel(FailReason $reason): string
    {
        return __('bot.fail_reason.' . $reason->value);
    }

    private function interruptLabel(InterruptionType $type): string
    {
        return __('bot.interrupt_type.' . $type->value);
    }

    /** @return Collection<int, Plan> */
    private function plansFor(User $user, CarbonImmutable $date): Collection
    {
        return Plan::query()
            ->where('user_id', $user->id)
            ->forDate($date)
            ->with('goal:id,title')
            ->orderBy('start_time')
            ->get();
    }

    private function account(int $chatId): ?TelegramAccount
    {
        return TelegramAccount::query()
            ->with('user')
            ->where('telegram_id', $chatId)
            ->where('is_active', true)
            ->first();
    }

    private function statusEmoji(PlanStatus $status): string
    {
        return match ($status) {
            PlanStatus::Completed => '✅',
            PlanStatus::Failed => '❌',
            PlanStatus::Postponed => '⏭',
            PlanStatus::Interrupted => '🏢',
            PlanStatus::NoResponse => '⚠️',
            PlanStatus::Cancelled => '🚫',
            default => '⬜',
        };
    }

    private function minutesUntilEvening(Plan $plan): int
    {
        $start = $plan->startsAt();
        $evening = $start->setTime(19, 0);

        return $evening->greaterThan($start)
            ? (int) $start->diffInMinutes($evening)
            : 120;
    }
}
