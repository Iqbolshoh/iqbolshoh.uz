<?php

namespace App\Models;

use App\Enums\PlanEventType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * An append-only entry in a plan's trail.
 *
 * The final status of a plan is not enough to forecast with: "planned 09:00 →
 * +10 → +10 → +30 → done at 09:50" describes a habit that a bare `completed`
 * hides. Rows here are never updated or deleted, so `updated_at` would be dead
 * weight — the table keeps `created_at` only.
 */
class PlanEvent extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'plan_id',
        'event_type',
        'from_time',
        'to_time',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'event_type' => PlanEventType::class,
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }
}
