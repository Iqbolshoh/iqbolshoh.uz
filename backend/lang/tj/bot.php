<?php

return [
    'private' => "Ин бот шахсӣ аст.\n\nTelegram ID-и шумо: <code>:id</code>.",
    'unknown' => "Дар ин ҷо маблағ нест.

Барои сабти пул <b>дар як сатр</b> нависед, ки чӣ ва чанд:
<code>хӯрок 25000</code> · <code>такси 12k</code> · <code>+маош 5 млн</code>

Тугмаҳои поён низ кор мекунанд.",

    'help' => [
        'title' => '❓ <b>Бот чӣ тавр кор мекунад</b>',
        'money_title' => '<b>1. Сабти пул</b>',
        'money' => "Дар як сатр нависед, ки чӣ ва чанд — ҳеҷ тугма лозим нест:\n<code>хӯрок 25000</code>\n<code>такси 12k</code>\n<code>коммуналӣ 350 ҳазор</code>\n\nБарои даромад дар пеш <b>+</b> гузоред ё «маош», «даромад» нависед:\n<code>+маош 5 млн</code>",
        'category_title' => '<b>2. Гурӯҳҳо</b>',
        'category' => 'Гурӯҳро аз рӯи калима муайян мекунам. Агар нафаҳмам, мепурсам ва ҷавоби шуморо дар ёд мегирам. Агар хато кунам, тугмаи <b>🏷 Иваз кардани гурӯҳ</b> ҳаст.',
        'plans_title' => '<b>3. Нақшаҳо</b>',
        'plans' => 'Нақшаҳо дар панел навишта мешаванд, бот бошад сари вақт ёдовар мешавад ва дар <b>📋 Имрӯз</b> нишон медиҳад. Банд бошед, <b>🚦 Ҳолат</b>-ро қайд кунед — ёдоварӣ хомӯш мемонад.',
        'careful' => '<i>Рақами дурақама маблағ ҳисоб намешавад: «саҳарӣ соати 8 давидан» нақша аст, на хароҷот.</i>',
    ],

    'btn' => [
        'today' => '📋 Имрӯз',
        'tomorrow' => '📅 Пагоҳ',
        'prev' => '◀️ Рӯзи қаблӣ',
        'next' => 'Рӯзи баъдӣ ▶️',
        'stats' => '📊 Омор',
        'status' => '🚦 Ҳолат',
        'refresh' => '🔄 Нав кардан',
        'back' => '◀️ Бозгашт',
        'done' => '✅ Иҷро шуд',
        'not_done' => '❌ Иҷро нашуд',
        'later' => '⏳ Дертар',
        'free_again' => '🏁 Озод шудам',
        'minutes' => '⏱ +:count дақ',
        'hour' => '🕐 +1 соат',
        'evening' => '🌙 Бегоҳӣ',
        'rest_of_day' => '🌇 То охири рӯз',
        'money' => '💰 Пул',
        'week' => '🗓 Ин ҳафта',
        'month' => '📆 Ин моҳ',
        'recent' => '🧾 Сабтҳои охирин',
        'undo' => '🗑 Нест кардан',
        'skip' => '⏭ Гузарондан',
        'change_category' => '🏷 Иваз кардани гурӯҳ',
        'all_categories' => '🗂 Ҳамаи гурӯҳҳо',
        'add_expense' => '➕ Хароҷот',
        'add_income' => '➕ Даромад',
        'today_money' => '📅 Имрӯз аз рӯи пул',
        'help' => '❓ Кумак',
        'language' => '🌐 Забон',
        'home' => '🏠 Асосӣ',
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
        'updated' => '<i>🕐 Навсозӣ: :time</i>',
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
        'week_so_far' => 'Ин ҳафта: <b>:amount</b>',
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
        'week_title' => '📅 <b>Ин ҳафта</b>',
        'recent_title' => '🧾 <b>Сабтҳои охирин</b>',
        'empty_recent' => 'Ҳанӯз сабте нест.',
        'empty_week' => 'Ин ҳафта ҳанӯз чизе сабт нашудааст.',
        'entries' => 'сабтҳо: :count',
        'pace' => 'Бо ин суръат то охири моҳ: <b>:amount</b>',
        'uncategorised' => 'Бе гурӯҳ',
        'pick_category' => 'Ба кадом гурӯҳ сабт кунам?',
        'note_line' => '<i>:note</i>',
        'category_changed' => '🏷 Гурӯҳ иваз шуд.',
        'how_to_add' => '<i>Бо тугма нависед ё дар як сатр: <code>хӯрок 25000</code></i>',
        'pick_for_expense' => 'Ба чӣ сарф кардед?',
        'pick_for_income' => 'Даромад аз куҷо?',
        'ask_amount' => "<b>:category</b> — чанд?\n\nТанҳо рақамро нависед: <code>25000</code>, <code>12k</code> ё <code>350 ҳазор</code>.",
        'day_title' => '📅 <b>Имрӯз</b>',
        'empty_day' => 'Имрӯз ҳанӯз чизе сабт нашудааст.',
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
        'help' => 'Бот чӣ тавр кор мекунад',
    ],
];
