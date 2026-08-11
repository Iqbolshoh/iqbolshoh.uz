<?php

namespace App\Models;

use App\Enums\InterruptionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A stretch of time the owner was not available.
 *
 * While one is open the reminder engine stays quiet, and the plans it swallowed
 * are marked as interrupted rather than failed — the difference the monthly
 * report is built on.
 */
class Interruption extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'started_at',
        'ends_at',
        'ended_at',
        'duration_minutes',
        'affected_plans',
    ];

    protected function casts(): array
    {
        return [
            'type' => InterruptionType::class,
            'started_at' => 'datetime',
            'ends_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plans(): HasMany
    {
        return $this->hasMany(Plan::class);
    }

    /**
     * Still running: never explicitly ended, and either open-ended or not yet
     * past the time the owner said they would be free.
     */
    public function scopeActive(Builder $query): void
    {
        $query->whereNull('ended_at')
            ->where(function (Builder $inner) {
                $inner->whereNull('ends_at')->orWhere('ends_at', '>', now());
            });
    }

    public function isActive(): bool
    {
        return $this->ended_at === null
            && ($this->ends_at === null || $this->ends_at->isFuture());
    }
}
