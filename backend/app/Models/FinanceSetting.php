<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * The owner's own rules for their money, per account.
 *
 * Separate from PlanSetting on purpose: the evening plan summary and the
 * evening spending prompt are two different conversations, and one being
 * switched off must never silence the other.
 */
class FinanceSetting extends Model
{
    protected $fillable = [
        'user_id',
        'monthly_budget',
        'warn_at_percent',
        'daily_prompt',
        'prompt_time',
        'weekly_report',
        'monthly_report',
    ];

    protected function casts(): array
    {
        return [
            'monthly_budget' => 'integer',
            'warn_at_percent' => 'integer',
            'daily_prompt' => 'boolean',
            'weekly_report' => 'boolean',
            'monthly_report' => 'boolean',
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
