<?php

use App\Console\Commands\CloseFinishedInterruptions;
use App\Console\Commands\SendDailySummary;
use App\Console\Commands\SendFinancePrompt;
use App\Console\Commands\SendFinanceReport;
use App\Console\Commands\SendPlanReminders;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Scheduled work
|--------------------------------------------------------------------------
| Nothing here runs without the `iqbolshoh-schedule` supervisor process. If
| reminders stop arriving, that is the first thing to check.
*/

// Every minute, because a reminder that is five minutes late is a reminder for
// the wrong moment. `withoutOverlapping` keeps a slow run from stacking.
Schedule::command(SendPlanReminders::class)
    ->everyMinute()
    ->withoutOverlapping(5);

// One summary at the end of the day, per account, at the hour each of them set.
Schedule::command(SendDailySummary::class)
    ->hourly()
    ->withoutOverlapping(10);

// The money side. Both run hourly for the same reason the daily summary does:
// each account has its own send hour on its own clock, and the server is UTC.
Schedule::command(SendFinancePrompt::class)
    ->hourly()
    ->withoutOverlapping(10);

Schedule::command(SendFinanceReport::class)
    ->hourly()
    ->withoutOverlapping(10);

// A status the owner set and never came back from still took the time it said
// it would. Every quarter hour is close enough for something measured in
// halves and wholes of an hour, and it keeps the time log filling itself.
Schedule::command(CloseFinishedInterruptions::class)
    ->everyFifteenMinutes()
    ->withoutOverlapping(10);
