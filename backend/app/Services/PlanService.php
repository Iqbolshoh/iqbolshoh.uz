<?php

namespace App\Services;

use App\Enums\FailReason;
use App\Enums\PlanEventType;
use App\Enums\PlanStatus;
use App\Enums\PostponeReason;
use App\Models\Plan;
use App\Models\PlanEvent;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

/**
 * The only place a plan changes state.
 *
 * Three callers close plans — the admin panel, the Telegram bot and the
 * scheduler — and each transition has to write its event in the same
 * transaction as the update. Keeping that in one class is what stops the trail
 * from developing holes that the forecast would silently read as data.
 */
class PlanService
{
    /** @param  array<string, mixed>  $attributes */
    public function create(array $attributes): Plan
    {
        return DB::transaction(function () use ($attributes): Plan {
            $plan = Plan::query()->create($attributes + ['status' => PlanStatus::Pending]);

            $plan->update(['next_reminder_at' => $plan->startsAt()->utc()]);

            $this->record($plan, PlanEventType::Created);

            return $plan;
        });
    }

    /** @param  array<string, mixed>  $attributes */
    public function update(Plan $plan, array $attributes): Plan
    {
        return DB::transaction(function () use ($plan, $attributes): Plan {
            $movedTime = isset($attributes['start_time'])
                && $attributes['start_time'] !== substr((string) $plan->start_time, 0, 5);

            $from = substr((string) $plan->start_time, 0, 5);

            $plan->update($attributes);

            // Re-arm the reminder whenever the plan is still open, so editing a
            // time in the panel never leaves a stale trigger behind.
            if (! $plan->status->isClosed()) {
                $plan->update([
                    'next_reminder_at' => $plan->startsAt()->utc(),
                    'reminder_count' => 0,
                ]);
            }

            if ($movedTime) {
                $this->record($plan, PlanEventType::Rescheduled, [
                    'from_time' => $from,
                    'to_time' => $attributes['start_time'],
                ]);
            }

            return $plan;
        });
    }

    public function complete(Plan $plan, ?int $actualMinutes = null): Plan
    {
        return DB::transaction(function () use ($plan, $actualMinutes): Plan {
            $plan->update([
                'status' => PlanStatus::Completed,
                'actual_minutes' => $actualMinutes ?? $this->elapsedMinutes($plan),
                'completed_at' => now(),
                'next_reminder_at' => null,
            ]);

            $this->record($plan, PlanEventType::Completed);

            return $plan;
        });
    }

    public function fail(Plan $plan, FailReason $reason = FailReason::Other): Plan
    {
        return DB::transaction(function () use ($plan, $reason): Plan {
            $plan->update([
                'status' => PlanStatus::Failed,
                'fail_reason' => $reason,
                'next_reminder_at' => null,
            ]);

            $this->record($plan, PlanEventType::Failed, ['reason' => $reason->value]);

            return $plan;
        });
    }

    /**
     * Push a plan forward. `$reason` distinguishes the owner's own decision
     * from a move an interruption forced, which is the difference the monthly
     * report is built on.
     */
    public function postpone(
        Plan $plan,
        int $minutes,
        PostponeReason $reason = PostponeReason::Self_,
        ?int $interruptionId = null,
    ): Plan {
        return DB::transaction(function () use ($plan, $minutes, $reason, $interruptionId): Plan {
            $from = $plan->startsAt();
            $to = $from->addMinutes($minutes);

            $plan->update([
                'date' => $to->toDateString(),
                'start_time' => $to->format('H:i:s'),
                'status' => $reason === PostponeReason::Interruption
                    ? PlanStatus::Interrupted
                    : PlanStatus::Pending,
                'postpone_reason' => $reason,
                'interruption_id' => $interruptionId,
                'postpone_count' => $plan->postpone_count + 1,
                'reminder_count' => 0,
                'next_reminder_at' => $to->utc(),
            ]);

            $this->record($plan, $reason === PostponeReason::Interruption
                ? PlanEventType::Interrupted
                : PlanEventType::Postponed, [
                    'from_time' => $from->format('H:i'),
                    'to_time' => $to->format('H:i'),
                    'minutes' => $minutes,
                ]);

            return $plan;
        });
    }

    /** The reminder engine gave up: no answer inside the configured window. */
    public function markNoResponse(Plan $plan): Plan
    {
        return DB::transaction(function () use ($plan): Plan {
            $plan->update([
                'status' => PlanStatus::NoResponse,
                'next_reminder_at' => null,
            ]);

            $this->record($plan, PlanEventType::NoResponse);

            return $plan;
        });
    }

    /** @param  array<string, mixed>  $metadata */
    private function record(Plan $plan, PlanEventType $type, array $metadata = []): void
    {
        PlanEvent::query()->create([
            'plan_id' => $plan->id,
            'event_type' => $type,
            'from_time' => $metadata['from_time'] ?? $plan->start_time,
            'to_time' => $metadata['to_time'] ?? null,
            'metadata' => $metadata ?: null,
        ]);
    }

    /**
     * How long the plan actually took, when nobody typed a number: the gap
     * between its start and now, capped at twice what was planned so a plan
     * ticked off the next morning does not report a twelve-hour session.
     */
    private function elapsedMinutes(Plan $plan): int
    {
        $started = $plan->started_at ?? $plan->startsAt();
        $elapsed = (int) $started->diffInMinutes(CarbonImmutable::now(), absolute: true);

        return max(1, min($elapsed, $plan->planned_minutes * 2));
    }
}
