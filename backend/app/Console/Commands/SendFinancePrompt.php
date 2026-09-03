<?php

namespace App\Console\Commands;

use App\Enums\NotificationKind;
use App\Enums\TransactionKind;
use App\Models\FinanceSetting;
use App\Models\Notification;
use App\Models\TelegramAccount;
use App\Models\Transaction;
use App\Services\FinanceBot;
use App\Services\TelegramClient;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

/**
 * The evening nudge: "what did today cost?".
 *
 * Runs hourly rather than at a fixed time so each account's own `prompt_time`
 * is honoured on its own clock — the server is UTC and Samarkand is five hours
 * ahead, so a fixed server-side 21:00 would arrive at four in the afternoon.
 *
 * It stays quiet on a day that already has spending recorded. A tracker that
 * asks anyway teaches its owner to ignore it, and then it stops working on the
 * days it was needed.
 */
class SendFinancePrompt extends Command
{
    protected $signature = 'finance:prompt {--force : Ask regardless of the hour and of what is already logged}';

    protected $description = 'Ask each account what the day cost';

    public function __construct(
        private readonly TelegramClient $client,
        private readonly FinanceBot $bot,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        if (! $this->client->isConfigured()) {
            return self::SUCCESS;
        }

        $settings = FinanceSetting::query()->with('user')->where('daily_prompt', true)->get();
        $sent = 0;

        foreach ($settings as $setting) {
            $user = $setting->user;

            if ($user === null) {
                continue;
            }

            $now = CarbonImmutable::now($user->timezone);
            $due = (int) substr((string) $setting->prompt_time, 0, 2);

            if (! $this->option('force') && $now->hour !== $due) {
                continue;
            }

            $account = TelegramAccount::query()
                ->where('user_id', $user->id)
                ->where('is_active', true)
                ->first();

            if ($account === null) {
                continue;
            }

            if (! $this->option('force') && $this->settledToday($user->id, $now)) {
                continue;
            }

            if ($this->alreadyAskedToday($user->id, $now)) {
                continue;
            }

            // The notification row goes in first, under the same guard the plan
            // reminders use: a second run of the same hour fails the insert and
            // sends nothing.
            $notification = Notification::query()->create([
                'user_id' => $user->id,
                'kind' => NotificationKind::FinancePrompt,
                'sequence' => 0,
                'title' => 'Spending prompt — ' . $now->format('j F'),
                'chat_id' => $account->telegram_id,
            ]);

            App::setLocale($account->locale ?? config('app.locale'));

            $this->bot->sendPrompt($account->telegram_id, $user);

            $notification->markSent(null);
            $sent++;
        }

        if ($sent > 0) {
            $this->info("Asked {$sent} accounts.");
        }

        return self::SUCCESS;
    }

    /** Anything already written down for today means there is nothing to ask about. */
    private function settledToday(int $userId, CarbonImmutable $today): bool
    {
        return Transaction::query()
            ->where('user_id', $userId)
            ->ofKind(TransactionKind::Expense)
            ->whereDate('date', $today->toDateString())
            ->exists();
    }

    private function alreadyAskedToday(int $userId, CarbonImmutable $now): bool
    {
        return Notification::query()
            ->where('user_id', $userId)
            ->where('kind', NotificationKind::FinancePrompt)
            ->whereDate('created_at', $now->utc()->toDateString())
            ->exists();
    }
}
