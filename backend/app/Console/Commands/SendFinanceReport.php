<?php

namespace App\Console\Commands;

use App\Enums\NotificationKind;
use App\Models\FinanceSetting;
use App\Models\Notification;
use App\Models\TelegramAccount;
use App\Models\Transaction;
use App\Services\FinanceStats;
use App\Services\TelegramClient;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

/**
 * The week that closed, and the month that closed.
 *
 * Both are sent at 09:00 on the owner's own clock — a report about a period
 * that has ended is not urgent, and one that arrives at midnight is read the
 * next morning anyway, by which point it has scrolled away.
 *
 * The period reported is always the previous one. A "this month" report on the
 * 1st would be a report about a few hours.
 */
class SendFinanceReport extends Command
{
    protected $signature = 'finance:report
                            {--weekly : Send the weekly report regardless of the day}
                            {--monthly : Send the monthly report regardless of the day}';

    protected $description = 'Send the weekly and monthly money reports';

    /** The hour, on the owner's clock, both reports go out at. */
    private const SEND_HOUR = 9;

    public function __construct(private readonly TelegramClient $client)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        if (! $this->client->isConfigured()) {
            return self::SUCCESS;
        }

        foreach (FinanceSetting::query()->with('user')->get() as $setting) {
            $user = $setting->user;

            if ($user === null) {
                continue;
            }

            $account = TelegramAccount::query()
                ->where('user_id', $user->id)
                ->where('is_active', true)
                ->first();

            if ($account === null) {
                continue;
            }

            $now = CarbonImmutable::now($user->timezone);
            $onTime = $now->hour === self::SEND_HOUR;

            if ($setting->weekly_report && ($this->option('weekly') || ($onTime && $now->isMonday()))) {
                $this->send(
                    $user->id,
                    $account,
                    NotificationKind::FinanceWeekly,
                    $now->subWeek()->startOfWeek(),
                    $now->subWeek()->endOfWeek(),
                    $now,
                );
            }

            if ($setting->monthly_report && ($this->option('monthly') || ($onTime && $now->day === 1))) {
                $this->send(
                    $user->id,
                    $account,
                    NotificationKind::FinanceMonthly,
                    $now->subMonth()->startOfMonth(),
                    $now->subMonth()->endOfMonth(),
                    $now,
                );
            }
        }

        return self::SUCCESS;
    }

    private function send(
        int $userId,
        TelegramAccount $account,
        NotificationKind $kind,
        CarbonImmutable $from,
        CarbonImmutable $to,
        CarbonImmutable $now,
    ): void {
        if ($this->alreadySent($userId, $kind, $from)) {
            return;
        }

        $stats = new FinanceStats($userId, $account->user?->timezone);
        $summary = $stats->summary($from, $to);

        // A period with nothing in it is not worth a message.
        if ($summary['count'] === 0) {
            return;
        }

        App::setLocale($account->locale ?? config('app.locale'));

        $lines = [
            __('bot.fin.month_title', [
                'month' => $kind === NotificationKind::FinanceMonthly
                    ? $from->translatedFormat('F Y')
                    : $from->translatedFormat('j M') . ' — ' . $to->translatedFormat('j M'),
            ]),
            '',
            __('bot.fin.expense', ['amount' => Transaction::money($summary['expense'])]),
            __('bot.fin.income', ['amount' => Transaction::money($summary['income'])]),
            __('bot.fin.balance', ['amount' => Transaction::money($summary['balance'])]),
            '',
            __('bot.fin.by_category'),
        ];

        foreach ($stats->byCategory($from, $to)->take(8) as $row) {
            $lines[] = sprintf(
                '%s — %s (%s%%)',
                $row['category']?->label() ?? '—',
                Transaction::money($row['total']),
                $row['share']
            );
        }

        $body = implode("\n", $lines);

        $notification = Notification::query()->create([
            'user_id' => $userId,
            'kind' => $kind,
            'sequence' => 0,
            'title' => $kind->label() . ' — ' . $from->format('Y-m-d'),
            'body' => $body,
            'chat_id' => $account->telegram_id,
        ]);

        $response = $this->client->sendMessage($account->telegram_id, $body);

        $response?->json('ok') === true
            ? $notification->markSent($response->json('result.message_id'))
            : $notification->markFailed('Telegram API: ' . ($response?->json('description') ?? 'no response'));
    }

    /**
     * One report per period, not per run.
     *
     * Matched on the period's own start date inside the title, because the
     * unique key on the table is built for plan reminders and cannot express
     * "one per week per account".
     */
    private function alreadySent(int $userId, NotificationKind $kind, CarbonImmutable $periodStart): bool
    {
        return Notification::query()
            ->where('user_id', $userId)
            ->where('kind', $kind)
            ->where('title', 'like', '%' . $periodStart->format('Y-m-d'))
            ->exists();
    }
}
