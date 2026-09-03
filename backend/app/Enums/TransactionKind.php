<?php

namespace App\Enums;

/**
 * Which way the money went.
 *
 * Kept as its own enum rather than a boolean because the two sides are not
 * mirror images: an expense can breach a limit and an income never can, and a
 * sign flip in a query is far easier to miss than a named case.
 */
enum TransactionKind: string
{
    case Expense = 'expense';
    case Income = 'income';

    public function label(): string
    {
        return match ($this) {
            self::Expense => 'Expense',
            self::Income => 'Income',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Expense => '#EF4444',
            self::Income => '#22C55E',
        };
    }

    /** The sign this side contributes to a balance. */
    public function sign(): int
    {
        return $this === self::Income ? 1 : -1;
    }

    /** Translation key for the bot, which speaks four languages. */
    public function translationKey(): string
    {
        return 'finance.kind.' . $this->value;
    }
}
