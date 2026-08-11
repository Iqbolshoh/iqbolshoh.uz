<?php

namespace App\Enums;

enum Priority: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::Low => '#6B7280',
            self::Medium => '#0EA5E9',
            self::High => '#EF4444',
        };
    }

    /** Sort weight, so "high" comes first in a listing ordered by priority. */
    public function weight(): int
    {
        return match ($this) {
            self::High => 0,
            self::Medium => 1,
            self::Low => 2,
        };
    }
}
