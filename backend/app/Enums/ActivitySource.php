<?php

namespace App\Enums;

/**
 * Where a time entry came from.
 *
 * `Status` is the one that matters: the bot writes those itself when an
 * interruption ends, and an hour it inferred must be distinguishable from an
 * hour the owner reported. Undo only ever reaches the owner's own rows, and
 * the report can say which part of the day was measured rather than typed.
 */
enum ActivitySource: string
{
    case Web = 'web';
    case Telegram = 'telegram';
    case Status = 'status';

    public function label(): string
    {
        return match ($this) {
            self::Web => 'Admin panel',
            self::Telegram => 'Telegram',
            self::Status => 'Status',
        };
    }
}
