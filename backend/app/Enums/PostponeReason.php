<?php

namespace App\Enums;

/**
 * Who moved the plan. This single distinction is what separates the raw
 * completion rate from the true one in every monthly report.
 */
enum PostponeReason: string
{
    case Self_ = 'self';
    case Interruption = 'interruption';

    public function label(): string
    {
        return match ($this) {
            self::Self_ => 'Postponed by me',
            self::Interruption => 'External interruption',
        };
    }
}
