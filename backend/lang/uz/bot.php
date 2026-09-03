<?php

return [
    'private' => "Bu bot shaxsiy.\n\nSizning Telegram ID: <code>:id</code>.",
    'unknown' => "Tushunmadim.\n\nPul yozish uchun shunday yozing: <code>ovqat 25000</code>. Yoki quyidagi tugmalardan foydalaning.",

    'btn' => [
        'today' => '📋 Bugun',
        'tomorrow' => '📅 Ertaga',
        'stats' => '📊 Statistika',
        'status' => '🚨 Holat',
        'refresh' => '🔄 Yangilash',
        'back' => '← Orqaga',
        'done' => '✅ Bajarildi',
        'not_done' => '❌ Bajarilmadi',
        'later' => '⏭ Keyinroq',
        'free_again' => "✅ Bo'shadim",
        'minutes' => '⏱ +:count daq',
        'hour' => '+1 soat',
        'evening' => '🌙 Kechqurun',
        'tomorrow_short' => '📅 Ertaga',
        'rest_of_day' => 'Kun oxirigacha',
        'money' => '💰 Pul',
        'month' => '📆 Shu oy',
        'undo' => "↩️ Bekor qilish",
        'skip' => "O'tkazib yuborish",
        'language' => '🌐 Til',
    ],

    'welcome' => [
        'title' => '👋 <b>Reja va Pul</b>',
        'plans' => 'Bugun: :total reja, :done bajarildi.',
        'spent' => 'Bugun sarflandi: <b>:amount</b>',
        'ask' => 'Nima qilamiz?',
        'hint' => "<code>ovqat 25000</code> deb yozsangiz, darrov yozib qo'yaman.",
    ],

    'day' => [
        'empty' => 'Reja yo\'q.',
        'settled' => '✅ :done/:settled bajarildi · :rate%',
        'nothing_settled' => 'Hali hech narsa yakunlanmadi.',
    ],

    'stats' => [
        'title' => '📊 <b>Sizning raqamlaringiz</b>',
        'week' => '<b>Shu hafta</b>',
        'month' => '<b>Shu oy</b>',
        'plans' => 'Rejalar: :total · Bajarildi: :completed',
        'rate' => 'Ko\'rsatkich: :raw% (haqiqiy :true%)',
        'time' => '⏱ Rejalashtirilgan :planned · Amalda :actual',
    ],

    'plan' => [
        'gone' => '⚠️ Bu reja endi mavjud emas.',
        'pushed' => '↩️ :count marta suriltirilgan',
        'status' => '<i>Holat: :status</i>',
        'fail_question' => 'Nima xalaqit berdi?',
        'later_question' => 'Qachon qaytaray?',
    ],

    'interrupt' => [
        'title' => '🚨 <b>Holatingizni belgilang</b>',
        'hint' => 'Band bo\'lgan vaqtingizda eslatmalar jim turadi.',
        'how_long' => 'Qancha vaqt band bo\'lasiz?',
        'until' => ':time gacha. Eslatmalar to\'xtatildi.',
        'moved' => ':count ta reja surildi.',
        'nothing_moved' => 'Surishga hech narsa yo\'q edi.',
        'untouched' => 'Qolgan rejalaringiz tegilmadi — qaytganingizda o\'zingiz hal qilasiz.',
    ],

    'plan_status' => [
        'pending' => 'Kutilmoqda',
        'in_progress' => 'Jarayonda',
        'completed' => 'Bajarildi',
        'failed' => 'Bajarilmadi',
        'postponed' => 'Kechiktirildi',
        'interrupted' => 'Uzildi',
        'no_response' => 'Javob yo\'q',
        'cancelled' => 'Bekor qilindi',
    ],

    'fail_reason' => [
        'no_time' => 'Vaqt yetmadi',
        'forgot' => 'Esimdan chiqdi',
        'overloaded' => 'Ish ko\'p edi',
        'not_important' => 'Muhim emas edi',
        'other' => 'Boshqa',
    ],

    'interrupt_type' => [
        'meeting' => 'Uchrashuvda',
        'travel' => 'Yo\'lda',
        'guest' => 'Mehmon bilan',
        'class' => 'Darsda',
        'work' => 'Ish bilan band',
        'rest' => 'Dam olyapman',
        'emergency' => 'Shoshilinch',
        'other' => 'Boshqa',
    ],

    'fin' => [
        'saved' => '✅ <b>:amount</b> · :category',
        'saved_uncategorised' => '✅ <b>:amount</b> yozib olindi.',
        'ask_category' => 'Qaysi turkumga?',
        'today' => 'Bugun: <b>:amount</b>',
        'month_so_far' => 'Shu oy: <b>:amount</b>',
        'left' => ':budget dan :amount qoldi',
        'over' => ':budget chegarasidan <b>:amount oshdi</b>',
        'no_budget' => 'Oylik chegara belgilanmagan.',
        'warning' => '⚠️ <b>:category</b> — chegaraning :used% i (:limit dan :total).',
        'undone' => '↩️ O\'chirildi: :amount · :category',
        'nothing_to_undo' => 'Bekor qiladigan yozuvim qolmadi.',
        'learned' => "Yodda tutdim — bundan keyin “:word” ni :category deb o'qiyman.",
        'prompt' => "🌙 Bugun qancha sarfladingiz?\n\nShunday yozing: <code>ovqat 25000</code>, har biri alohida qatorda. Sarflamagan bo'lsangiz ham javob bo'ladi.",
        'nothing_today' => 'Bugun hech narsa yozilmadi.',
        'title' => '💰 <b>Pul</b>',
        'month_title' => '📆 <b>:month</b>',
        'by_category' => 'Nimaga ketdi:',
        'income' => 'Kirim: <b>:amount</b>',
        'expense' => 'Chiqim: <b>:amount</b>',
        'balance' => 'Balans: <b>:amount</b>',
        'empty_month' => 'Bu oyda hali hech narsa yozilmadi.',
    ],

    'summary' => [
        'daily' => '📋 <b>Kunlik xulosa — :date</b>',
        'plans_line' => '📋 :total reja · ✅ :completed bajarildi · :rate%',
        'money_line' => '💰 Sarflandi :amount',
    ],

    'lang' => [
        'ask' => '🌐 Tilni tanlang:',
        'set' => '✅ Til o\'zbekchaga o\'zgartirildi.',
    ],
];
