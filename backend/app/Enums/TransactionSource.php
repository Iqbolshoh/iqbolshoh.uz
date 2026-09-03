<?php

namespace App\Enums;

/**
 * Where a row was entered.
 *
 * The bot needs it for "undo the last one I added": it must never withdraw a
 * row the owner typed into the admin panel from another device.
 */
enum TransactionSource: string
{
    case Web = 'web';
    case Telegram = 'telegram';

    public function label(): string
    {
        return match ($this) {
            self::Web => 'Admin panel',
            self::Telegram => 'Telegram',
        };
    }
}
