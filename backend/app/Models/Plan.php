<?php

namespace App\Models;

use App\Enums\FailReason;
use App\Enums\PlanStatus;
use App\Enums\PostponeReason;
use App\Enums\Priority;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * One thing to do at one time on one day.
 *
 * The plan carries its own reminder state rather than delegating it to a queue
 * table: the scheduler then finds everything that is due with a single indexed
 * query per minute, and a plan can never drift out of sync with its reminders.
 *
 * `date` and `start_time` are wall-clock in the owner's timezone; every
 * timestamp column is UTC.
 */
class Plan extends Model
{
    protected $fillable = [
        'user_id',
        'goal_id',
        'title',
        'description',
        'date',
        'start_time',
        'planned_minutes',
        'actual_minutes',
        'status',
        'priority',
        'postpone_reason',
        'fail_reason',
        'interruption_id',
        'reminder_count',
        'next_reminder_at',
        'last_reminded_at',
        'started_at',
        'completed_at',
        'postpone_count',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'status' => PlanStatus::class,
            'priority' => Priority::class,
            'postpone_reason' => PostponeReason::class,
            'fail_reason' => FailReason::class,
            'next_reminder_at' => 'datetime',
            'last_reminded_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }

    public function interruption(): BelongsTo
    {
        return $this->belongsTo(Interruption::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(PlanEvent::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function scopeForDate(Builder $query, CarbonInterface|string $date): void
    {
        $query->whereDate('date', Carbon::parse($date));
    }

    public function scopeBetween(Builder $query, CarbonInterface $from, CarbonInterface $to): void
    {
        $query->whereBetween('date', [$from->toDateString(), $to->toDateString()]);
    }

    /** Plans the reminder engine may still act on. */
    public function scopeOpen(Builder $query): void
    {
        $query->whereIn('status', array_column(PlanStatus::open(), 'value'));
    }

    /** The absolute instant this plan starts, resolved in the owner's timezone. */
    public function startsAt(?string $timezone = null): Carbon
    {
        $zone = $timezone ?? $this->user?->timezone ?? config('app.timezone');

        return Carbon::parse(
            $this->date->toDateString() . ' ' . $this->start_time,
            $zone
        );
    }

    public function endsAt(?string $timezone = null): Carbon
    {
        return $this->startsAt($timezone)->addMinutes($this->planned_minutes);
    }

    /** "1h 30m" — used wherever a duration is shown next to a plan. */
    public static function humanMinutes(?int $minutes): string
    {
        if (! $minutes) {
            return '0m';
        }

        $hours = intdiv($minutes, 60);
        $rest = $minutes % 60;

        return match (true) {
            $hours === 0 => "{$rest}m",
            $rest === 0 => "{$hours}h",
            default => "{$hours}h {$rest}m",
        };
    }
}
