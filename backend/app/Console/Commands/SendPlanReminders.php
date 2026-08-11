<?php

namespace App\Console\Commands;

use App\Enums\NotificationKind;
use App\Enums\PlanStatus;
use App\Models\Interruption;
use App\Models\Notification;
use App\Models\Plan;
use App\Models\PlanSetting;
use App\Services\PlanService;
use App\Services\TelegramBot;
use App\Services\TelegramClient;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

/**
 * Runs every minute and sends the reminders that are due.
 *
 * Three things make this safe to run that often:
 *
 * 1. A plan is re-read inside a transaction with a row lock before anything is
 *    sent, so a plan ticked off in the seconds since the query never gets a
 *    reminder anyway.
 * 2. The notification row is inserted first, under a unique key. A second run
 *    of the same reminder fails that insert and sends nothing.
 * 3. Reminders older than the configured window are not sent at all. After an
 *    outage the backlog is settled quietly instead of arriving all at once.
 */
class SendPlanReminders extends Command
{
    protected $signature = 'plans:remind';

    protected $description = 'Send reminders for plans that are due';

    public function __construct(
        private readonly TelegramClient $client,
        private readonly TelegramBot $bot,
        private readonly PlanService $plans,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        if (! $this->client->isConfigured()) {
            return self::SUCCESS;
        }

        $due = Plan::query()
            ->whereIn('status', array_column(PlanStatus::open(), 'value'))
            ->whereNotNull('next_reminder_at')
            ->where('next_reminder_at', '<=', now())
            ->with('goal:id,title,color', 'user')
            ->orderBy('next_reminder_at')
            ->limit(50)
            ->get();

        $sent = 0;

        foreach ($due as $plan) {
            if ($this->isInterrupted($plan)) {
                // Push the trigger past the interruption rather than dropping
                // it, so the plan comes back the moment the owner is free.
                $plan->update(['next_reminder_at' => now()->addMinutes(5)]);

                continue;
            }

            $sent += $this->remind($plan) ? 1 : 0;
        }

        if ($sent > 0) {
            $this->info("Sent {$sent} reminders.");
        }

        return self::SUCCESS;
    }

    private function remind(Plan $plan): bool
    {
        $settings = PlanSetting::forUser($plan->user_id);

        $result = DB::transaction(function () use ($plan, $settings): ?Notification {
            /** @var Plan $fresh */
            $fresh = Plan::query()->lockForUpdate()->find($plan->id);

            if ($fresh === null || $fresh->status->isClosed() || $fresh->next_reminder_at === null) {
                return null;
            }

            $overdue = CarbonImmutable::parse($fresh->next_reminder_at)
                ->diffInMinutes(CarbonImmutable::now(), absolute: true);

            // Too old to be worth sending: settle it rather than shout about a
            // slot that passed hours ago.
            if ($overdue > $settings->max_reminder_window_minutes) {
                $this->plans->markNoResponse($fresh);

                return null;
            }

            if ($fresh->reminder_count >= $settings->max_reminders) {
                $this->plans->markNoResponse($fresh);

                return null;
            }

            $sequence = $fresh->reminder_count + 1;

            try {
                $notification = Notification::query()->create([
                    'user_id' => $fresh->user_id,
                    'plan_id' => $fresh->id,
                    'kind' => NotificationKind::Reminder,
                    'sequence' => $sequence,
                    'title' => $fresh->title,
                    'body' => null,
                    'chat_id' => config('services.telegram.chat_id') ?: null,
                ]);
            } catch (QueryException) {
                // The unique key did its job: this reminder already exists.
                return null;
            }

            $fresh->update([
                'reminder_count' => $sequence,
                'last_reminded_at' => now(),
                'next_reminder_at' => now()->addMinutes($settings->reminder_repeat_minutes),
            ]);

            return $notification;
        });

        if ($result === null) {
            return false;
        }

        $plan->refresh();

        $response = $this->client->sendMessage(
            $result->chat_id,
            $this->bot->planCard($plan),
            $this->bot->planKeyboard($plan)
        );

        if ($response?->json('ok') === true) {
            $result->markSent($response->json('result.message_id'));

            return true;
        }

        $result->markFailed('Telegram API: ' . ($response?->json('description') ?? 'no response'));

        return false;
    }

    private function isInterrupted(Plan $plan): bool
    {
        return Interruption::query()
            ->where('user_id', $plan->user_id)
            ->active()
            ->exists();
    }
}
