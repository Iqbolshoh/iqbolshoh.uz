<?php

namespace App\Enums;

/**
 * One entry in a plan's trail. The table is append-only: a plan's history is
 * what the forecast segments on, so nothing here is ever edited or removed.
 */
enum PlanEventType: string
{
    case Created = 'created';
    case Reminded = 'reminded';
    case Started = 'started';
    case Postponed = 'postponed';
    case Completed = 'completed';
    case Failed = 'failed';
    case NoResponse = 'no_response';
    case Interrupted = 'interrupted';
    case Rescheduled = 'rescheduled';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::NoResponse => 'No response',
            default => ucfirst($this->value),
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Created => 'plus',
            self::Reminded => 'bell',
            self::Started => 'play',
            self::Postponed => 'clock',
            self::Completed => 'check',
            self::Failed => 'x',
            self::NoResponse => 'bell-off',
            self::Interrupted => 'alert-triangle',
            self::Rescheduled => 'calendar',
            self::Cancelled => 'ban',
        };
    }
}
