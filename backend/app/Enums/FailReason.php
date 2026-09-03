<?php

namespace App\Enums;

enum FailReason: string
{
    case NoTime = 'no_time';
    case Forgot = 'forgot';
    case Overloaded = 'overloaded';
    case NotImportant = 'not_important';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::NoTime => 'Ran out of time',
            self::Forgot => 'Forgot',
            self::Overloaded => 'Too many plans',
            self::NotImportant => 'Was not important',
            self::Other => 'Other',
        };
    }

    public function emoji(): string
    {
        return match ($this) {
            self::NoTime => '⏰',
            self::Forgot => '💭',
            self::Overloaded => '🗂',
            self::NotImportant => '🚫',
            self::Other => '✍️',
        };
    }
}
