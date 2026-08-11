<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Links a Telegram chat to an account.
 *
 * The bot answers nobody who is not in this table: it is the whole access
 * control model, and it is why the bot can be left public on Telegram without
 * exposing anything.
 */
class TelegramAccount extends Model
{
    protected $fillable = [
        'user_id',
        'telegram_id',
        'username',
        'first_name',
        'is_active',
        'linked_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'linked_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
