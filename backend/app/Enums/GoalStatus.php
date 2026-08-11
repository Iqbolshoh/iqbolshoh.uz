<?php

namespace App\Enums;

enum GoalStatus: string
{
    case Active = 'active';
    case Achieved = 'achieved';
    case Missed = 'missed';
    case Archived = 'archived';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => '#0EA5E9',
            self::Achieved => '#22C55E',
            self::Missed => '#EF4444',
            self::Archived => '#6B7280',
        };
    }
}
