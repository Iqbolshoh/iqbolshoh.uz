<?php

namespace App\Console\Commands;

use App\Enums\NotificationKind;
use App\Models\Notification;
use App\Models\Plan;
use App\Models\PlanSetting;
use App\Services\PlanStats;
use App\Services\TelegramClient;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;

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

            $this->send($user->id, $now);
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

    private function send(int $userId, CarbonImmutable $today): void
    {
        $stats = new PlanStats($userId);
        $summary = $stats->summary($today->startOfDay(), $today->endOfDay());

        if ($summary['total'] === 0) {
            return;
        }

        $body = implode("\n", array_filter([
            "📋 {$summary['total']} plans",
            "✅ {$summary['completed']} completed",
            $summary['failed'] > 0 ? "❌ {$summary['failed']} failed" : null,
            $summary['interrupted'] > 0 ? "🏢 {$summary['interrupted']} interrupted" : null,
            $summary['no_response'] > 0 ? "⚠️ {$summary['no_response']} no response" : null,
            '',
            "📊 {$summary['raw_rate']}% completion",
            '⏱ Planned ' . Plan::humanMinutes($summary['planned_minutes'])
                . ' · Actual ' . Plan::humanMinutes($summary['actual_minutes']),
        ]));

        try {
            $notification = Notification::query()->create([
                'user_id' => $userId,
                'kind' => NotificationKind::DailySummary,
                'sequence' => 0,
                'title' => 'Daily summary — ' . $today->format('j F'),
                'body' => $body,
                'chat_id' => config('services.telegram.chat_id') ?: null,
            ]);
        } catch (QueryException) {
            return;
        }

        $response = $this->client->sendMessage(
            $notification->chat_id,
            '<b>' . e($notification->title) . "</b>\n\n" . e($body)
        );

        $response?->json('ok') === true
            ? $notification->markSent($response->json('result.message_id'))
            : $notification->markFailed('Telegram API: ' . ($response?->json('description') ?? 'no response'));
    }
}
