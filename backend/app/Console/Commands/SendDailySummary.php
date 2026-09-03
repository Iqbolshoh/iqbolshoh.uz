<?php

namespace App\Console\Commands;

use App\Enums\NotificationKind;
use App\Models\Notification;
use App\Models\Plan;
use App\Models\PlanSetting;
use App\Models\TelegramAccount;
use App\Models\Transaction;
use App\Services\PlanStats;
use App\Services\TelegramClient;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\App;

/**
 * The evening wrap-up, sent once per account at the hour it asked for.
 *
 * Runs hourly rather than at a fixed time so each account's own
 * `daily_summary_time` is honoured in its own timezone. The unique key on
 * `(plan_id, kind, sequence)` cannot help here — the summary has no plan — so
 * the guard is an explicit check for one already sent today.
 */
class SendDailySummary extends Command
{
    protected $signature = 'plans:daily-summary {--force : Send regardless of the configured hour}';

    protected $description = 'Send each account its daily summary';

    public function __construct(private readonly TelegramClient $client)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        if (! $this->client->isConfigured()) {
            return self::SUCCESS;
        }

        $settings = PlanSetting::query()->with('user')->where('daily_summary', true)->get();

        foreach ($settings as $setting) {
            $user = $setting->user;

            if ($user === null) {
                continue;
            }

            $now = CarbonImmutable::now($user->timezone);
            $due = (int) substr((string) $setting->daily_summary_time, 0, 2);

            if (! $this->option('force') && $now->hour !== $due) {
                continue;
            }

            if ($this->alreadySentToday($user->id, $now)) {
                continue;
            }

            $this->send($user, $now);
        }

        return self::SUCCESS;
    }

    private function alreadySentToday(int $userId, CarbonImmutable $now): bool
    {
        return Notification::query()
            ->where('user_id', $userId)
            ->where('kind', NotificationKind::DailySummary)
            ->whereDate('created_at', $now->utc()->toDateString())
            ->exists();
    }

    private function send(\App\Models\User $user, CarbonImmutable $today): void
    {
        $stats = new PlanStats($user->id);
        $summary = $stats->summary($today->startOfDay(), $today->endOfDay());

        $spent = (int) Transaction::query()
            ->where('user_id', $user->id)
            ->ofKind(\App\Enums\TransactionKind::Expense)
            ->whereDate('date', $today->toDateString())
            ->sum('amount');

        // A day with neither a plan nor a som spent has nothing to report.
        if ($summary['total'] === 0 && $spent === 0) {
            return;
        }

        $account = TelegramAccount::query()
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->first();

        $chatId = $account?->telegram_id ?? config('services.telegram.chat_id') ?: null;

        if ($chatId === null) {
            return;
        }

        App::setLocale($account?->locale ?? config('app.locale'));

        $body = implode("\n", array_filter([
            $summary['total'] > 0
                ? __('bot.summary.plans_line', [
                    'total' => $summary['total'],
                    'completed' => $summary['completed'],
                    'rate' => $summary['raw_rate'],
                ])
                : null,
            $summary['total'] > 0
                ? __('bot.stats.time', [
                    'planned' => Plan::humanMinutes($summary['planned_minutes']),
                    'actual' => Plan::humanMinutes($summary['actual_minutes']),
                ])
                : null,
            $spent > 0 ? __('bot.summary.money_line', ['amount' => Transaction::money($spent)]) : null,
        ]));

        try {
            $notification = Notification::query()->create([
                'user_id' => $user->id,
                'kind' => NotificationKind::DailySummary,
                'sequence' => 0,
                'title' => 'Daily summary — ' . $today->format('j F'),
                'body' => $body,
                'chat_id' => $chatId,
            ]);
        } catch (QueryException) {
            return;
        }

        $response = $this->client->sendMessage(
            $chatId,
            __('bot.summary.daily', ['date' => $today->translatedFormat('j F')]) . "\n\n" . $body
        );

        $response?->json('ok') === true
            ? $notification->markSent($response->json('result.message_id'))
            : $notification->markFailed('Telegram API: ' . ($response?->json('description') ?? 'no response'));
    }
}
