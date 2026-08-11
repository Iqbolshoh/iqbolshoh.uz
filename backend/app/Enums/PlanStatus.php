<?php

namespace App\Enums;

/**
 * Where a plan ended up.
 *
 * The four "not completed" cases are deliberately distinct: the forecast reads
 * them very differently. A plan the owner postponed says something about their
 * habits, one cut short by a meeting says nothing about them at all, and one
 * that never got an answer says the reminders are being ignored.
 */
enum PlanStatus: string
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Failed = 'failed';
    case Postponed = 'postponed';
    case Interrupted = 'interrupted';
    case NoResponse = 'no_response';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::InProgress => 'In progress',
            self::Completed => 'Completed',
            self::Failed => 'Failed',
            self::Postponed => 'Postponed',
            self::Interrupted => 'Interrupted',
            self::NoResponse => 'No response',
            self::Cancelled => 'Cancelled',
        };
    }

    /** Brand colour for the badge; the admin panel derives text, border and tint from it. */
    public function color(): string
    {
        return match ($this) {
            self::Pending => '#8B95A5',
            self::InProgress => '#0EA5E9',
            self::Completed => '#22C55E',
            self::Failed => '#EF4444',
            self::Postponed => '#F59E0B',
            self::Interrupted => '#6366F1',
            self::NoResponse => '#A855F7',
            self::Cancelled => '#6B7280',
        };
    }

    /** Statuses the reminder engine still has work to do for. */
    public static function open(): array
    {
        return [self::Pending, self::InProgress];
    }

    /** A plan in one of these is finished; nothing may reopen it automatically. */
    public function isClosed(): bool
    {
        return ! in_array($this, self::open(), true);
    }

    /**
     * Whether the plan counts against the owner in the "true" completion rate.
     * An interruption was not their doing, so it is excluded.
     */
    public function countsAgainstOwner(): bool
    {
        return in_array($this, [self::Failed, self::NoResponse], true);
    }
}
