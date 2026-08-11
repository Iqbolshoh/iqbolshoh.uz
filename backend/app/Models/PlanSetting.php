<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * How loudly the reminder engine is allowed to speak, per account.
 *
 * Defaults match the specification: the first reminder lands at the start time,
 * repeats every ten minutes, and gives up after four attempts or one hour —
 * whichever comes first.
 */
class PlanSetting extends Model
{
    protected $fillable = [
        'user_id',
        'reminder_repeat_minutes',
        'max_reminders',
        'max_reminder_window_minutes',
        'daily_summary',
        'weekly_summary',
        'monthly_report',
        'forecast',
        'daily_summary_time',
        'quiet_mode',
    ];

    protected function casts(): array
    {
        return [
            'daily_summary' => 'boolean',
            'weekly_summary' => 'boolean',
            'monthly_report' => 'boolean',
            'forecast' => 'boolean',
            'quiet_mode' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** The settings row for an account, created with the defaults on first use. */
    public static function forUser(int $userId): self
    {
        return self::firstOrCreate(['user_id' => $userId]);
    }
}
