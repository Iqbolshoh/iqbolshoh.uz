<?php

return [
    'private' => "Этот бот приватный.\n\nВаш Telegram ID: <code>:id</code>.",
    'unknown' => "Не понял.\n\nЧтобы записать расход, напишите так: <code>обед 25000</code>. Или воспользуйтесь кнопками ниже.",

    'btn' => [
        'today' => '📋 Сегодня',
        'tomorrow' => '📅 Завтра',
        'prev' => '◀️ Пред. день',
        'next' => 'След. день ▶️',
        'stats' => '📊 Статистика',
        'status' => '🚦 Статус',
        'refresh' => '🔄 Обновить',
        'back' => '◀️ Назад',
        'done' => '✅ Готово',
        'not_done' => '❌ Не сделал',
        'later' => '⏳ Позже',
        'free_again' => '🏁 Я освободился',
        'minutes' => '⏱ +:count мин',
        'hour' => '🕐 +1 час',
        'evening' => '🌙 Вечером',
        'rest_of_day' => '🌇 До конца дня',
        'money' => '💰 Деньги',
        'week' => '🗓 Эта неделя',
        'month' => '📆 Этот месяц',
        'recent' => '🧾 Последние записи',
        'undo' => '↩️ Отменить',
        'skip' => '⏭ Пропустить',
        'language' => '🌐 Язык',
        'home' => '🏠 Главная',
    ],

    'welcome' => [
        'title' => '👋 <b>План и Деньги</b>',
        'plans' => 'Сегодня: :total планов, :done выполнено.',
        'spent' => 'Потрачено сегодня: <b>:amount</b>',
        'ask' => 'Что делаем?',
        'hint' => 'Напишите <code>обед 25000</code> — запишу сразу.',
    ],

    'day' => [
        'empty' => 'Ничего не запланировано.',
        'settled' => '✅ :done/:settled выполнено · :rate%',
        'nothing_settled' => 'Пока ничего не закрыто.',
        'updated' => '<i>🕐 Обновлено: :time</i>',
    ],

    'stats' => [
        'title' => '📊 <b>Ваши цифры</b>',
        'week' => '<b>Эта неделя</b>',
        'month' => '<b>Этот месяц</b>',
        'plans' => 'Планов: :total · Выполнено: :completed',
        'rate' => 'Показатель: :raw% (честный :true%)',
        'time' => '⏱ Запланировано :planned · Фактически :actual',
    ],

    'plan' => [
        'gone' => '⚠️ Этого плана больше нет.',
        'pushed' => '↩️ Перенесён :count×',
        'status' => '<i>Статус: :status</i>',
        'fail_question' => 'Что помешало?',
        'later_question' => 'Когда напомнить снова?',
    ],

    'interrupt' => [
        'title' => '🚨 <b>Укажите статус</b>',
        'hint' => 'Пока вы заняты, напоминания молчат.',
        'how_long' => 'Насколько вы заняты?',
        'until' => 'До :time. Напоминания на паузе.',
        'moved' => 'Перенесено планов: :count.',
        'nothing_moved' => 'Переносить было нечего.',
        'untouched' => 'Остальные планы не тронуты — решите сами, когда вернётесь.',
    ],

    'plan_status' => [
        'pending' => 'Ожидает',
        'in_progress' => 'В процессе',
        'completed' => 'Выполнено',
        'failed' => 'Не выполнено',
        'postponed' => 'Перенесено',
        'interrupted' => 'Прервано',
        'no_response' => 'Без ответа',
        'cancelled' => 'Отменено',
    ],

    'fail_reason' => [
        'no_time' => 'Не хватило времени',
        'forgot' => 'Забыл',
        'overloaded' => 'Слишком много дел',
        'not_important' => 'Оказалось неважным',
        'other' => 'Другое',
    ],

    'interrupt_type' => [
        'meeting' => 'На встрече',
        'travel' => 'В дороге',
        'guest' => 'С гостями',
        'class' => 'На занятии',
        'work' => 'Занят работой',
        'rest' => 'Отдыхаю',
        'emergency' => 'Срочное дело',
        'other' => 'Другое',
    ],

    'fin' => [
        'saved' => '✅ <b>:amount</b> · :category',
        'saved_uncategorised' => '✅ <b>:amount</b> записано.',
        'ask_category' => 'В какую категорию?',
        'today' => 'Сегодня: <b>:amount</b>',
        'week_so_far' => 'Эта неделя: <b>:amount</b>',
        'month_so_far' => 'В этом месяце: <b>:amount</b>',
        'left' => 'Осталось :amount из :budget',
        'over' => '<b>Превышение на :amount</b> от лимита :budget',
        'no_budget' => 'Месячный лимит не задан.',
        'warning' => '⚠️ <b>:category</b> — :used% лимита (:total из :limit).',
        'undone' => '↩️ Удалено: :amount · :category',
        'nothing_to_undo' => 'Отменять больше нечего.',
        'learned' => 'Запомнил — теперь «:word» это :category.',
        'prompt' => "🌙 Сколько потратили сегодня?\n\nНапишите так: <code>обед 25000</code>, по одной строке. «Ничего» — тоже ответ.",
        'nothing_today' => 'Сегодня ничего не записано.',
        'title' => '💰 <b>Деньги</b>',
        'month_title' => '📆 <b>:month</b>',
        'by_category' => 'Куда ушло:',
        'income' => 'Приход: <b>:amount</b>',
        'expense' => 'Расход: <b>:amount</b>',
        'balance' => 'Баланс: <b>:amount</b>',
        'empty_month' => 'В этом месяце пока ничего нет.',
        'week_title' => '📅 <b>Эта неделя</b>',
        'recent_title' => '🧾 <b>Последние записи</b>',
        'empty_recent' => 'Записей пока нет.',
        'empty_week' => 'На этой неделе пока ничего.',
        'entries' => 'записей: :count',
        'pace' => 'Такими темпами к концу месяца: <b>:amount</b>',
    ],

    'summary' => [
        'daily' => '📋 <b>Итоги дня — :date</b>',
        'plans_line' => '📋 :total планов · ✅ :completed выполнено · :rate%',
        'money_line' => '💰 Потрачено :amount',
    ],

    'lang' => [
        'ask' => '🌐 Выберите язык:',
        'set' => '✅ Язык переключён на русский.',
    ],

    /*
    | What Telegram itself shows about the bot — the name on the chat header,
    | the text on an empty chat, and the "/" menu. None of it lives in the
    | repository until `telegram:profile` pushes it, so this is the source.
    | Plain text only: Telegram does not read HTML in a bot description.
    */
    'profile' => [
        'short' => "Планы, расходы и напоминания — всё в одном месте.",
        'description' => "Личный бот Иқболшоха: планы, расходы и напоминания в одном месте.

О плане напомню вовремя, расход пойму из одной строки: напишите \"еда 25000\" — и запись готова. Вечером сам спрошу, во что обошёлся день.

Бот закрытый: отвечает только своему владельцу.",
    ],

    'cmd' => [
        'menu' => 'Главный экран',
        'today' => 'Планы на сегодня',
        'tomorrow' => 'Планы на завтра',
        'status' => 'Сказать, что занят или свободен',
        'stats' => 'Как прошла неделя',
        'money' => 'Деньги: сегодня и этот месяц',
        'language' => 'Сменить язык',
    ],
];
