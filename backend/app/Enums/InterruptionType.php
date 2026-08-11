<?php

namespace App\Enums;

/**
 * Why the day stopped following the plan.
 *
 * Emergency is separate from the rest on purpose: every other type moves the
 * affected plans on its own, while an emergency always asks first.
 */
enum InterruptionType: string
{
    case Meeting = 'meeting';
    case Travel = 'travel';
    case Guest = 'guest';
    case Class_ = 'class';
    case Work = 'work';
    case Rest = 'rest';
    case Emergency = 'emergency';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Meeting => 'In a meeting',
            self::Travel => 'Travelling',
            self::Guest => 'With guests',
            self::Class_ => 'In class',
            self::Work => 'Busy with work',
            self::Rest => 'Resting',
            self::Emergency => 'Emergency',
            self::Other => 'Other',
        };
    }

    public function emoji(): string
    {
        return match ($this) {
            self::Meeting => '🏢',
            self::Travel => '🚗',
            self::Guest => '👥',
            self::Class_ => '📚',
            self::Work => '💼',
            self::Rest => '😴',
            self::Emergency => '🚨',
            self::Other => '✍️',
        };
    }

    /** An emergency never moves plans by itself — it asks what to do. */
    public function movesPlansAutomatically(): bool
    {
        return $this !== self::Emergency;
    }
}
