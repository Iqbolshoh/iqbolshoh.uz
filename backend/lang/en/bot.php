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
    'unknown' => "There is no amount in that.

To record money, write what and how much <b>on one line</b>:
<code>ovqat 25000</code> · <code>taksi 12k</code> · <code>+salary 5 mln</code>

The buttons below work too.",

    'help' => [
        'title' => '❓ <b>How the bot works</b>',
        'money_title' => '<b>1. Recording money</b>',
        'money' => "Write what and how much on one line — no buttons involved:\n<code>ovqat 25000</code>\n<code>taksi 12k</code>\n<code>kommunal 350 ming</code>\n\nFor income put a <b>+</b> in front, or say “salary”, “income”:\n<code>+salary 5 mln</code>",
        'category_title' => '<b>2. Categories</b>',
        'category' => 'The category comes from the words you used. When I cannot tell, I ask — and I remember your answer, so next time I know. When I get it wrong there is a <b>🏷 Change category</b> button.',
        'plans_title' => '<b>3. Plans</b>',
        'plans' => 'Plans are written in the panel; the bot reminds you on time and shows them under <b>📋 Today</b>. When you are busy, set <b>🚦 Status</b> and the reminders go quiet.',
        'careful' => '<i>A two-digit number is not treated as money: “ertalab 8 da yugurish” is a plan, not a payment.</i>',
    ],

    'btn' => [
        'today' => '📋 Today',
        'tomorrow' => '📅 Tomorrow',
        'prev' => '◀️ Prev day',
        'next' => 'Next day ▶️',
        'stats' => '📊 Stats',
        'status' => '🚦 Set status',
        'refresh' => '🔄 Refresh',
        'back' => '◀️ Back',
        'done' => '✅ Done',
        'not_done' => '❌ Not done',
        'later' => '⏳ Later',
        'free_again' => "🏁 I'm free again",
        'minutes' => '⏱ +:count min',
        'hour' => '🕐 +1 hour',
        'evening' => '🌙 This evening',
        'rest_of_day' => '🌇 Rest of the day',
        'money' => '💰 Money',
        'week' => '🗓 This week',
        'month' => '📆 This month',
        'recent' => '🧾 Recent entries',
        'undo' => '🗑 Delete',
        'skip' => '⏭ Skip',
        'change_category' => '🏷 Change category',
        'all_categories' => '🗂 All categories',
        'add_expense' => '➕ Expense',
        'add_income' => '➕ Income',
        'today_money' => '📅 Today\'s money',
        'time' => '⏱ Time',
        'add_time' => '➕ Log time',
        'act_today' => '📅 Today\'s time',
        'stats_today' => '📅 Today',
        'stats_week' => '🗓 Week',
        'stats_month' => '📆 Month',
        'tasks' => '📋 Tasks',
        'help' => '❓ Help',
        'language' => '🌐 Language',
        'home' => '🏠 Home',
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
        'updated' => '<i>🕐 Updated: :time</i>',
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
        'week_so_far' => 'This week: <b>:amount</b>',
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
        'week_title' => '📅 <b>This week</b>',
        'recent_title' => '🧾 <b>Recent entries</b>',
        'empty_recent' => 'Nothing recorded yet.',
        'empty_week' => 'Nothing recorded this week yet.',
        'entries' => ':count entries',
        'pace' => 'At this pace, by month end: <b>:amount</b>',
        'uncategorised' => 'Uncategorised',
        'pick_category' => 'Which category should this go to?',
        'note_line' => '<i>:note</i>',
        'category_changed' => '🏷 Category changed.',
        'how_to_add' => '<i>Use the buttons, or write one line: <code>ovqat 25000</code></i>',
        'pick_for_expense' => 'What did you spend it on?',
        'pick_for_income' => 'Where did it come from?',
        'ask_amount' => "<b>:category</b> — how much?\n\nJust the number: <code>25000</code>, <code>12k</code> or <code>350 ming</code>.",
        'day_title' => '📅 <b>Today</b>',
        'empty_day' => 'Nothing recorded today yet.',
    ],

    'report' => [
        'title_today' => '📊 <b>Today\'s report</b>',
        'title_week' => '📊 <b>This week\'s report</b>',
        'title_month' => '📊 <b>This month\'s report</b>',
        'when' => '<i>:range</i>',
        'tasks' => '✅ <b>TASKS</b>',
        'tasks_line' => '<code>:bar</code> :done/:settled · <b>:rate%</b>',
        'tasks_pending' => '⏳ :count still open',
        'tasks_none' => '<i>No plans</i>',
        'money' => '💰 <b>MONEY</b>',
        'money_out' => '🔻 Spent: <b>:amount</b>',
        'money_in' => '🔺 Received: <b>:amount</b>',
        'money_none' => '<i>Nothing recorded</i>',
        'time' => '⏱ <b>TIME</b>',
        'time_line' => '<code>:bar</code> <b>:duration</b> · :percent% accounted for',
        'time_balance' => '✨ Useful <b>:good</b> · 🌀 Wasted <b>:bad</b>',
        'time_none' => '<i>Nothing logged</i>',
        'top' => 'Most of it:',
    ],

    'act' => [
        'title' => '⏱ <b>Time</b>',
        'today' => 'Today: <b>:duration</b>',
        'week_so_far' => 'This week: <b>:duration</b>',
        'month_so_far' => 'This month: <b>:duration</b>',
        'covered' => '<b>:percent%</b> of the day accounted for',
        'covered_period' => '<b>:percent%</b> of the period accounted for',
        'average' => 'Per day on average: <b>:duration</b>',
        'by_category' => 'Where it went:',
        'saved' => '✅ <b>:duration</b> · :category',
        'saved_uncategorised' => '✅ <b>:duration</b> logged.',
        'ask_category' => 'What were you doing?',
        'pick_for_new' => 'What did the time go on?',
        'ask_duration' => "<b>:category</b> — how long?\n\nFor example: <code>2 soat</code>, <code>45 daqiqa</code> or <code>1 soat 30 daq</code>.",
        'how_to_add' => '<i>Use the buttons, or write one line: <code>8 soat uxladim</code></i>',
        'day_title' => '📅 <b>Today\'s time</b>',
        'week_title' => '🗓 <b>This week</b>',
        'month_title' => '📆 <b>:month</b>',
        'empty_day' => 'No time logged today yet.',
        'empty_week' => 'No time logged this week yet.',
        'empty_month' => 'No time logged this month yet.',
        'recent_title' => '🧾 <b>Recent time entries</b>',
        'empty_recent' => 'No time entries yet.',
        'entries' => ':count entries',
        'undone' => '🗑 Removed: :duration · :category',
        'nothing_to_undo' => 'There is no time entry left to remove.',
        'learned' => 'Noted — “:word” means :category from now on.',
        'targets' => 'Targets:',
        'target_line' => ':category — <b>:spent</b> of :target',
        'balance' => 'Useful <b>:good</b> · wasted <b>:bad</b>',
        'from_status' => 'from status',
        'uncategorised' => 'Uncategorised',
        'pick_category' => 'Which activity should this go to?',
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
        'help' => 'How the bot works',
        'time' => 'Time: day, week, month',
    ],
];
