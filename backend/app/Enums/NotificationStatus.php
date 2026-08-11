<?php

namespace App\Enums;

enum NotificationStatus: string
{
    case Pending = 'pending';
    case Sent = 'sent';
    case Failed = 'failed';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => '#F59E0B',
            self::Sent => '#22C55E',
            self::Failed => '#EF4444',
        };
    }
}
