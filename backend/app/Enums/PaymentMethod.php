<?php

namespace App\Enums;

/**
 * How it was paid.
 *
 * Worth recording because cash is the part of a month that goes missing: a
 * card total can always be reconciled against a statement later, cash cannot
 * be reconstructed at all if it was not written down at the time.
 */
enum PaymentMethod: string
{
    case Cash = 'cash';
    case Card = 'card';
    case Transfer = 'transfer';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Cash => 'Cash',
            self::Card => 'Card',
            self::Transfer => 'Transfer',
            self::Other => 'Other',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Cash => '💵',
            self::Card => '💳',
            self::Transfer => '🏦',
            self::Other => '•',
        };
    }

    public function translationKey(): string
    {
        return 'finance.method.' . $this->value;
    }
}
