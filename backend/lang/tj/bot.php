<?php

return [
    'private' => "Ин бот шахсӣ аст.\n\nTelegram ID-и шумо: <code>:id</code>.",
    'unknown' => "Нафаҳмидам.\n\nБарои сабти хароҷот чунин нависед: <code>хӯрок 25000</code>. Ё аз тугмаҳои поён истифода баред.",

    'btn' => [
        'today' => '📋 Имрӯз',
        'tomorrow' => '📅 Пагоҳ',
        'stats' => '📊 Омор',
        'status' => '🚨 Ҳолат',
        'refresh' => '🔄 Нав кардан',
        'back' => '← Бозгашт',
        'done' => '✅ Иҷро шуд',
        'not_done' => '❌ Иҷро нашуд',
        'later' => '⏭ Дертар',
        'free_again' => '✅ Озод шудам',
        'minutes' => '⏱ +:count дақ',
        'hour' => '+1 соат',
        'evening' => '🌙 Бегоҳӣ',
        'tomorrow_short' => '📅 Пагоҳ',
        'rest_of_day' => 'То охири рӯз',
        'money' => '💰 Пул',
        'month' => '📆 Ин моҳ',
        'undo' => '↩️ Бекор кардан',
        'skip' => 'Гузарондан',
        'language' => '🌐 Забон',
    ],

    'welcome' => [
        'title' => '👋 <b>Нақша ва Пул</b>',
        'plans' => 'Имрӯз: :total нақша, :done иҷро шуд.',
        'spent' => 'Имрӯз сарф шуд: <b>:amount</b>',
        'ask' => 'Чӣ кор кунем?',
        'hint' => '<code>хӯрок 25000</code> нависед — фавран сабт мекунам.',
    ],

    'day' => [
        'empty' => 'Нақша нест.',
        'settled' => '✅ :done/:settled иҷро шуд · :rate%',
        'nothing_settled' => 'Ҳанӯз чизе анҷом наёфт.',
    ],

    'stats' => [
        'title' => '📊 <b>Рақамҳои шумо</b>',
        'week' => '<b>Ин ҳафта</b>',
        'month' => '<b>Ин моҳ</b>',
        'plans' => 'Нақшаҳо: :total · Иҷро шуд: :completed',
        'rate' => 'Нишондиҳанда: :raw% (аслӣ :true%)',
        'time' => '⏱ Ба нақша :planned · Дар амал :actual',
    ],

    'plan' => [
        'gone' => '⚠️ Ин нақша дигар вуҷуд надорад.',
        'pushed' => '↩️ :count маротиба гузаронида шуд',
        'status' => '<i>Ҳолат: :status</i>',
        'fail_question' => 'Чӣ халал расонд?',
        'later_question' => 'Кай бармегардонам?',
    ],

    'interrupt' => [
        'title' => '🚨 <b>Ҳолати худро нишон диҳед</b>',
        'hint' => 'Вақте банд ҳастед, ёдоварӣ хомӯш мемонад.',
        'how_long' => 'Чанд вақт банд мешавед?',
        'until' => 'То :time. Ёдоварӣ таваққуф ёфт.',
        'moved' => ':count нақша гузаронида шуд.',
        'nothing_moved' => 'Чизе барои гузарондан набуд.',
        'untouched' => 'Нақшаҳои боқимонда бетағйир монданд — баъди баргаштан худатон қарор мекунед.',
    ],

    'plan_status' => [
        'pending' => 'Дар интизор',
        'in_progress' => 'Дар ҷараён',
        'completed' => 'Иҷро шуд',
        'failed' => 'Иҷро нашуд',
        'postponed' => 'Мавқуф гузошта шуд',
        'interrupted' => 'Қатъ шуд',
        'no_response' => 'Бе ҷавоб',
        'cancelled' => 'Бекор шуд',
    ],

    'fail_reason' => [
        'no_time' => 'Вақт нарасид',
        'forgot' => 'Фаромӯш кардам',
        'overloaded' => 'Кор аз ҳад зиёд буд',
        'not_important' => 'Муҳим набуд',
        'other' => 'Дигар',
    ],

    'interrupt_type' => [
        'meeting' => 'Дар вохӯрӣ',
        'travel' => 'Дар роҳ',
        'guest' => 'Бо меҳмон',
        'class' => 'Дар дарс',
        'work' => 'Бо кор банд',
        'rest' => 'Истироҳат мекунам',
        'emergency' => 'Кори таъҷилӣ',
        'other' => 'Дигар',
    ],

    'fin' => [
        'saved' => '✅ <b>:amount</b> · :category',
        'saved_uncategorised' => '✅ <b>:amount</b> сабт шуд.',
        'ask_category' => 'Ба кадом гурӯҳ?',
        'today' => 'Имрӯз: <b>:amount</b>',
        'month_so_far' => 'Ин моҳ: <b>:amount</b>',
        'left' => 'Аз :budget боқӣ :amount',
        'over' => 'Аз ҳудуди :budget <b>:amount зиёд шуд</b>',
        'no_budget' => 'Ҳудуди моҳона муқаррар нашудааст.',
        'warning' => '⚠️ <b>:category</b> — :used% ҳудуд (аз :limit — :total).',
        'undone' => '↩️ Нест карда шуд: :amount · :category',
        'nothing_to_undo' => 'Барои бекор кардан чизе намондааст.',
        'learned' => 'Дар хотир гирифтам — минбаъд «:word»-ро :category мехонам.',
        'prompt' => "🌙 Имрӯз чанд сарф кардед?\n\nЧунин нависед: <code>хӯрок 25000</code>, ҳар яке дар сатри алоҳида. «Ҳеҷ чиз» ҳам ҷавоб аст.",
        'nothing_today' => 'Имрӯз чизе сабт нашуд.',
        'title' => '💰 <b>Пул</b>',
        'month_title' => '📆 <b>:month</b>',
        'by_category' => 'Ба чӣ сарф шуд:',
        'income' => 'Даромад: <b>:amount</b>',
        'expense' => 'Хароҷот: <b>:amount</b>',
        'balance' => 'Тавозун: <b>:amount</b>',
        'empty_month' => 'Дар ин моҳ ҳанӯз чизе нест.',
    ],

    'summary' => [
        'daily' => '📋 <b>Хулосаи рӯз — :date</b>',
        'plans_line' => '📋 :total нақша · ✅ :completed иҷро шуд · :rate%',
        'money_line' => '💰 Сарф шуд :amount',
    ],

    'lang' => [
        'ask' => '🌐 Забонро интихоб кунед:',
        'set' => '✅ Забон ба тоҷикӣ иваз шуд.',
    ],

    /*
    | What Telegram itself shows about the bot — the name on the chat header,
    | the text on an empty chat, and the "/" menu. None of it lives in the
    | repository until `telegram:profile` pushes it, so this is the source.
    | Plain text only: Telegram does not read HTML in a bot description.
    */
    'profile' => [
        'short' => "Нақшаҳо, хароҷот ва ёдоварӣ — ҳама дар як ҷо.",
        'description' => "Боти шахсии Иқболшоҳ: нақшаҳо, хароҷот ва ёдоварӣ дар як ҷо.

Нақшаро сари вақт ба ёдатон меорам, хароҷотро аз як сатр мефаҳмам: \"хӯрок 25000\" нависед, сабт мешавад. Бегоҳӣ худам мепурсам, ки рӯз чанд пул арзид.

Бот пӯшида аст: танҳо бо соҳибаш сӯҳбат мекунад.",
    ],

    'cmd' => [
        'menu' => 'Саҳифаи асосӣ',
        'today' => 'Нақшаҳои имрӯз',
        'tomorrow' => 'Нақшаҳои пагоҳ',
        'status' => 'Гуфтан, ки банд ё озодед',
        'stats' => 'Ҳафта чӣ гуна гузашт',
        'money' => 'Пул: имрӯз ва ин моҳ',
        'language' => 'Иваз кардани забон',
    ],
];
