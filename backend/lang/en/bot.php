<?php

/**
 * Everything the Telegram bot says.
 *
 * The admin panel is English only and never reads this file — these lines
 * exist so the bot can answer in whichever of the four languages the owner
 * last chose.
 *
 * Placeholders are named, never positional: a translator moving ":amount"
 * across a sentence must not have to think about argument order.
 */
return [
    'private' => "This bot is private.\n\nYour Telegram ID is <code>:id</code>.",
    'unknown' => "I did not understand that.\n\nTo record money, write it like <code>ovqat 25000</code>. Or use the buttons below.",

    'btn' => [
        'today' => '📋 Today',
        'tomorrow' => '📅 Tomorrow',
        'stats' => '📊 Stats',
        'status' => '🚨 Set status',
        'refresh' => '🔄 Refresh',
        'back' => '← Back',
        'done' => '✅ Done',
        'not_done' => '❌ Not done',
        'later' => '⏭ Later',
        'free_again' => "✅ I'm free again",
        'minutes' => '⏱ +:count min',
        'hour' => '+1 hour',
        'evening' => '🌙 This evening',
        'tomorrow_short' => '📅 Tomorrow',
        'rest_of_day' => 'Rest of the day',
        'money' => '💰 Money',
        'month' => '📆 This month',
        'undo' => '↩️ Undo',
        'skip' => 'Skip',
        'language' => '🌐 Language',
    ],

    'welcome' => [
        'title' => '👋 <b>Plan &amp; Money</b>',
        'plans' => 'Today: :total plans, :done done.',
        'spent' => 'Spent today: <b>:amount</b>',
        'ask' => 'What would you like to do?',
        'hint' => 'Write <code>ovqat 25000</code> and it is recorded straight away.',
    ],

    'day' => [
        'empty' => 'Nothing scheduled.',
        'settled' => '✅ :done/:settled done · :rate%',
        'nothing_settled' => 'Nothing settled yet.',
    ],

    'stats' => [
        'title' => '📊 <b>Your numbers</b>',
        'week' => '<b>This week</b>',
        'month' => '<b>This month</b>',
        'plans' => 'Plans: :total · Completed: :completed',
        'rate' => 'Rate: :raw% (true :true%)',
        'time' => '⏱ Planned :planned · Actual :actual',
    ],

    'plan' => [
        'gone' => '⚠️ That plan no longer exists.',
        'pushed' => '↩️ Pushed :count×',
        'status' => '<i>Status: :status</i>',
        'fail_question' => 'What got in the way?',
        'later_question' => 'When should it come back?',
    ],

    'interrupt' => [
        'title' => '🚨 <b>Set your status</b>',
        'hint' => 'While you are busy, reminders stay quiet.',
        'how_long' => 'How long will you be busy?',
        'until' => 'Until :time. Reminders are paused.',
        'moved' => ':count plans moved out of the way.',
        'nothing_moved' => 'Nothing needed moving.',
        'untouched' => 'Your remaining plans are untouched — decide what to do with them when you are back.',
    ],

    'plan_status' => [
        'pending' => 'Pending',
        'in_progress' => 'In progress',
        'completed' => 'Completed',
        'failed' => 'Failed',
        'postponed' => 'Postponed',
        'interrupted' => 'Interrupted',
        'no_response' => 'No response',
        'cancelled' => 'Cancelled',
    ],

    'fail_reason' => [
        'no_time' => 'No time',
        'forgot' => 'Forgot',
        'overloaded' => 'Too much on',
        'not_important' => 'Not important',
        'other' => 'Other',
    ],

    'interrupt_type' => [
        'meeting' => 'In a meeting',
        'travel' => 'Travelling',
        'guest' => 'With guests',
        'class' => 'In class',
        'work' => 'Busy with work',
        'rest' => 'Resting',
        'emergency' => 'Emergency',
        'other' => 'Other',
    ],

    'fin' => [
        'saved' => '✅ <b>:amount</b> · :category',
        'saved_uncategorised' => '✅ <b>:amount</b> recorded.',
        'ask_category' => 'Which category?',
        'today' => 'Today: <b>:amount</b>',
        'month_so_far' => 'This month: <b>:amount</b>',
        'left' => ':amount left of :budget',
        'over' => '<b>:amount over</b> the :budget budget',
        'no_budget' => 'No monthly ceiling set.',
        'warning' => '⚠️ <b>:category</b> — :used% of its limit (:total of :limit).',
        'undone' => '↩️ Removed: :amount · :category',
        'nothing_to_undo' => 'Nothing of mine left to undo.',
        'learned' => "Noted — I will read “:word” as :category from now on.",
        'prompt' => "🌙 What did today cost?\n\nWrite it like <code>ovqat 25000</code>, one line per thing. Nothing to report is an answer too.",
        'nothing_today' => 'Nothing recorded today.',
        'title' => '💰 <b>Money</b>',
        'month_title' => '📆 <b>:month</b>',
        'by_category' => 'Where it went:',
        'income' => 'In: <b>:amount</b>',
        'expense' => 'Out: <b>:amount</b>',
        'balance' => 'Balance: <b>:amount</b>',
        'empty_month' => 'Nothing recorded this month yet.',
    ],

    'summary' => [
        'daily' => '📋 <b>Daily summary — :date</b>',
        'plans_line' => '📋 :total plans · ✅ :completed done · :rate%',
        'money_line' => '💰 Spent :amount',
    ],

    'lang' => [
        'ask' => '🌐 Choose your language:',
        'set' => '✅ Language set to English.',
    ],

    /*
    | What Telegram itself shows about the bot — the name on the chat header,
    | the text on an empty chat, and the "/" menu. None of it lives in the
    | repository until `telegram:profile` pushes it, so this is the source.
    | Plain text only: Telegram does not read HTML in a bot description.
    */
    'profile' => [
        'short' => "Plans, spending and reminders — all in one place.",
        'description' => "Iqbolshoh's private bot: plans, spending and reminders in one place.

I remind you of a plan on time, and read spending straight from one line: write \"lunch 25000\" and it is filed. In the evening I ask what the day cost.

The bot is closed: it talks to its owner only.",
    ],

    'cmd' => [
        'menu' => 'Home screen',
        'today' => "Today's plans",
        'tomorrow' => "Tomorrow's plans",
        'status' => 'Say you are busy or free',
        'stats' => 'How the week went',
        'money' => 'Money: today and this month',
        'language' => 'Change language',
    ],
];
