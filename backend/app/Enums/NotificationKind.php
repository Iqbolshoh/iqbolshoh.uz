<?php

namespace App\Enums;

enum NotificationKind: string
{
    case Reminder = 'reminder';
    case DailySummary = 'daily_summary';
    case WeeklySummary = 'weekly_summary';
    case MonthlyReport = 'monthly_report';
    case Forecast = 'forecast';
    case SiteContact = 'site_contact';
    case SiteOrder = 'site_order';
    case Interruption = 'interruption';

    // Money. Kept as their own kinds rather than reusing the plan ones so the
    // notification log stays readable, and so switching the plan summary off
    // can never silence the spending prompt.
    case FinancePrompt = 'finance_prompt';
    case FinanceWeekly = 'finance_weekly';
    case FinanceMonthly = 'finance_monthly';

    public function label(): string
    {
        return match ($this) {
            self::Reminder => 'Reminder',
            self::DailySummary => 'Daily summary',
            self::WeeklySummary => 'Weekly summary',
            self::MonthlyReport => 'Monthly report',
            self::Forecast => 'Forecast',
            self::SiteContact => 'Contact message',
            self::SiteOrder => 'Service order',
            self::Interruption => 'Interruption',
            self::FinancePrompt => 'Spending prompt',
            self::FinanceWeekly => 'Weekly money report',
            self::FinanceMonthly => 'Monthly money report',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Reminder => 'bell',
            self::DailySummary => 'sun',
            self::WeeklySummary => 'calendar-days',
            self::MonthlyReport => 'file-text',
            self::Forecast => 'trending-up',
            self::SiteContact => 'mail',
            self::SiteOrder => 'shopping-bag',
            self::Interruption => 'alert-triangle',
            self::FinancePrompt => 'wallet',
            self::FinanceWeekly => 'chart-column',
            self::FinanceMonthly => 'receipt',
        };
    }
}
