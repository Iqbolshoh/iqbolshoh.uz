<?php

namespace App\Services;

use App\Enums\FailReason;
use App\Enums\InterruptionType;
use App\Enums\PlanStatus;
use App\Enums\PostponeReason;
use App\Models\Interruption;
use App\Models\Plan;
use App\Models\TelegramAccount;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
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
    ) {}

    /** @param  array<string, mixed>  $message */
    public function handleMessage(array $message): void
    {
        $chatId = (int) ($message['chat']['id'] ?? 0);
        $text = trim((string) ($message['text'] ?? ''));

        $account = $this->account($chatId);

        if ($account === null) {
            $this->client->sendMessage($chatId, "This bot is private.\n\nYour Telegram ID is <code>{$chatId}</code>.");

            return;
        }

        $command = Str::of($text)->lower()->before(' ')->trim()->toString();

        match ($command) {
            '/start', '/menu' => $this->sendWelcome($chatId, $account->user),
            '/today' => $this->sendDay($chatId, $account->user, CarbonImmutable::today($account->user->timezone)),
            '/tomorrow' => $this->sendDay($chatId, $account->user, CarbonImmutable::tomorrow($account->user->timezone)),
            '/status' => $this->sendInterruptionMenu($chatId),
            '/stats' => $this->sendStats($chatId, $account->user),
            default => $this->sendWelcome($chatId, $account->user),
        };
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

        $parts = explode(':', $data);

        match ($parts[0] ?? '') {
            'p' => $this->handlePlanAction($chatId, $messageId, $account->user, $parts),
            'i' => $this->handleInterruptionAction($chatId, $messageId, $account->user, $parts),
            'nav' => $this->handleNavigation($chatId, $messageId, $account->user, $parts),
            default => null,
        };
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
            $lines[] = "↩️ Pushed {$plan->postpone_count}×";
        }

        $lines[] = '';
        $lines[] = '<i>Status: ' . $plan->status->label() . '</i>';

        return implode("\n", $lines);
    }

    /** @return array<string, mixed> */
    public function planKeyboard(Plan $plan): array
    {
        if ($plan->status->isClosed()) {
            return TelegramClient::keyboard([
                [TelegramClient::button('📋 Today', 'nav:today')],
            ]);
        }

        return TelegramClient::keyboard([
            [
                TelegramClient::button('✅ Done', "p:{$plan->id}:done"),
                TelegramClient::button('❌ Not done', "p:{$plan->id}:fail"),
            ],
            [
                TelegramClient::button('⏱ +10 min', "p:{$plan->id}:pp:10"),
                TelegramClient::button('⏱ +30 min', "p:{$plan->id}:pp:30"),
            ],
            [
                TelegramClient::button('⏭ Later', "p:{$plan->id}:later"),
                TelegramClient::button('📋 Today', 'nav:today'),
            ],
        ]);
    }

    private function sendWelcome(int $chatId, User $user): void
    {
        $today = CarbonImmutable::today($user->timezone);
        $plans = $this->plansFor($user, $today);
        $done = $plans->where('status', PlanStatus::Completed)->count();

        $text = implode("\n", [
            '👋 <b>Plan</b>',
            '',
            "Today: {$plans->count()} plans, {$done} done.",
            '',
            'What would you like to do?',
        ]);

        $this->client->sendMessage($chatId, $text, TelegramClient::keyboard([
            [
                TelegramClient::button("📋 Today's plans", 'nav:today'),
                TelegramClient::button('📅 Tomorrow', 'nav:tomorrow'),
            ],
            [
                TelegramClient::button('📊 Stats', 'nav:stats'),
                TelegramClient::button('🚨 Set status', 'nav:status'),
            ],
        ]));
    }

    private function sendDay(int $chatId, User $user, CarbonImmutable $date, ?int $editMessageId = null): void
    {
        $plans = $this->plansFor($user, $date);

        if ($plans->isEmpty()) {
            $text = '📋 <b>' . $date->format('l, j F') . "</b>\n\nNothing scheduled.";
            $keyboard = TelegramClient::keyboard([[TelegramClient::button('📅 Tomorrow', 'nav:tomorrow')]]);

            $editMessageId
                ? $this->client->editMessage($chatId, $editMessageId, $text, $keyboard)
                : $this->client->sendMessage($chatId, $text, $keyboard);

            return;
        }

        $lines = ['📋 <b>' . $date->format('l, j F') . '</b>', ''];
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
            ? "✅ {$done}/{$settled} done · " . round($done / max(1, $settled) * 100) . '%'
            : 'Nothing settled yet.';

        $rows[] = [
            TelegramClient::button('🔄 Refresh', 'nav:today'),
            TelegramClient::button('🚨 Set status', 'nav:status'),
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
            '📊 <b>Your numbers</b>',
            '',
            '<b>This week</b>',
            "Plans: {$week['total']} · Completed: {$week['completed']}",
            "Rate: {$week['raw_rate']}% (true {$week['true_rate']}%)",
            '',
            '<b>This month</b>',
            "Plans: {$month['total']} · Completed: {$month['completed']}",
            "Rate: {$month['raw_rate']}% (true {$month['true_rate']}%)",
            '',
            '⏱ Planned ' . Plan::humanMinutes($month['planned_minutes'])
                . ' · Actual ' . Plan::humanMinutes($month['actual_minutes']),
        ]);

        $keyboard = TelegramClient::keyboard([[TelegramClient::button('📋 Today', 'nav:today')]]);

        $editMessageId
            ? $this->client->editMessage($chatId, $editMessageId, $text, $keyboard)
            : $this->client->sendMessage($chatId, $text, $keyboard);
    }

    private function sendInterruptionMenu(int $chatId, ?int $editMessageId = null): void
    {
        $text = "🚨 <b>Set your status</b>\n\nWhile you are busy, reminders stay quiet.";

        $rows = [];
        $buttons = [];

        foreach (InterruptionType::cases() as $type) {
            $buttons[] = TelegramClient::button($type->emoji() . ' ' . $type->label(), "i:start:{$type->value}");

            if (count($buttons) === 2) {
                $rows[] = $buttons;
                $buttons = [];
            }
        }

        if ($buttons !== []) {
            $rows[] = $buttons;
        }

        $rows[] = [TelegramClient::button('📋 Back to today', 'nav:today')];

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
            $this->client->editMessage($chatId, $messageId, '⚠️ That plan no longer exists.');

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
            $buttons[] = TelegramClient::button($reason->emoji() . ' ' . $reason->label(), "p:{$plan->id}:fail:{$reason->value}");

            if (count($buttons) === 2) {
                $rows[] = $buttons;
                $buttons = [];
            }
        }

        if ($buttons !== []) {
            $rows[] = $buttons;
        }

        $rows[] = [TelegramClient::button('← Back', "p:{$plan->id}:open")];

        $this->client->editMessage(
            $chatId,
            $messageId,
            '❌ <b>' . e($plan->title) . "</b>\n\nWhat got in the way?",
            TelegramClient::keyboard($rows)
        );
    }

    private function askPostponeWindow(int $chatId, int $messageId, Plan $plan): void
    {
        $this->client->editMessage(
            $chatId,
            $messageId,
            '⏭ <b>' . e($plan->title) . "</b>\n\nWhen should it come back?",
            TelegramClient::keyboard([
                [
                    TelegramClient::button('+30 min', "p:{$plan->id}:pp:30"),
                    TelegramClient::button('+1 hour', "p:{$plan->id}:pp:60"),
                ],
                [
                    TelegramClient::button('🌙 This evening', "p:{$plan->id}:pp:" . $this->minutesUntilEvening($plan)),
                    TelegramClient::button('📅 Tomorrow', "p:{$plan->id}:pp:1440"),
                ],
                [TelegramClient::button('← Back', "p:{$plan->id}:open")],
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
            $type->emoji() . ' <b>' . $type->label() . "</b>\n\nHow long will you be busy?",
            TelegramClient::keyboard([
                [
                    TelegramClient::button('30 min', "i:for:{$type->value}:30"),
                    TelegramClient::button('1 hour', "i:for:{$type->value}:60"),
                ],
                [
                    TelegramClient::button('2 hours', "i:for:{$type->value}:120"),
                    TelegramClient::button('Rest of the day', "i:for:{$type->value}:600"),
                ],
                [TelegramClient::button('← Back', 'nav:status')],
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

        $text = $type->emoji() . ' <b>' . $type->label() . "</b>\n\n"
            . 'Until ' . $startedAt->addMinutes($minutes)->format('H:i') . ". Reminders are paused.\n\n"
            . ($type->movesPlansAutomatically()
                ? ($affected > 0 ? "{$affected} plans moved out of the way." : 'Nothing needed moving.')
                : 'Your remaining plans are untouched — decide what to do with them when you are back.');

        $this->client->editMessage($chatId, $messageId, $text, TelegramClient::keyboard([
            [TelegramClient::button("✅ I'm free again", "i:end:{$interruption->id}")],
            [TelegramClient::button('📋 Today', 'nav:today')],
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
            default => null,
        };
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
