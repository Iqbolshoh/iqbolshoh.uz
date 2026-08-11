<?php

namespace App\Models;

use App\Enums\NotificationKind;
use App\Enums\NotificationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Every message the system sends, whether it arrived or not.
 *
 * Sending goes through this table rather than straight to Telegram, which buys
 * three things at once: a history the owner can read, a retry that knows what
 * to resend, and idempotency — `(plan_id, kind, sequence)` is unique, so a job
 * that runs twice fails its second insert instead of sending twice.
 */
class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'plan_id',
        'kind',
        'sequence',
        'title',
        'body',
        'channel',
        'status',
        'chat_id',
        'message_id',
        'attempts',
        'error',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'kind' => NotificationKind::class,
            'status' => NotificationStatus::class,
            'sent_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function scopeFailed(Builder $query): void
    {
        $query->where('status', NotificationStatus::Failed);
    }

    public function markSent(?int $messageId = null): void
    {
        $this->forceFill([
            'status' => NotificationStatus::Sent,
            'message_id' => $messageId,
            'error' => null,
            'sent_at' => now(),
        ])->save();
    }

    public function markFailed(string $error): void
    {
        $this->forceFill([
            'status' => NotificationStatus::Failed,
            'error' => mb_substr($error, 0, 1000),
            'attempts' => $this->attempts + 1,
        ])->save();
    }
}
