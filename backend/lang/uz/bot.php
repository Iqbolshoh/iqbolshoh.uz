<?php

return [
    'private' => "Bu bot shaxsiy.\n\nSizning Telegram ID: <code>:id</code>.",
    'unknown' => "Bunda summa ko'rinmadi.

Pul yozish uchun <b>bir qatorda</b> nima va qancha ekanini yozing:
<code>ovqat 25000</code> · <code>taksi 12k</code> · <code>+oylik 5 mln</code>

Quyidagi tugmalar ham ishlaydi.",

    'help' => [
        'title' => '❓ <b>Bot qanday ishlaydi</b>',
        'money_title' => '<b>1. Pulni yozish</b>',
        'money' => "Bir qatorda nima va qancha ekanini yozing — tugma bosish shart emas:\n<code>ovqat 25000</code>\n<code>taksi 12k</code>\n<code>kommunal 350 ming</code>\n\nKirim uchun oldiga <b>+</b> qo'ying yoki “oylik”, “daromad” deb yozing:\n<code>+oylik 5 mln</code>",
        'category_title' => '<b>2. Turkumlar</b>',
        'category' => 'So\'zdan turkumni o\'zim topaman. Topa olmasam so\'rayman — javobingizni yodda tutib, keyingi safar o\'zim bilaman. Noto\'g\'ri tushunsam <b>🏷 Turkumni almashtirish</b> tugmasi bor.',
        'plans_title' => '<b>3. Rejalar</b>',
        'plans' => 'Rejalar panelda yoziladi, bot esa vaqti kelganda eslatadi va <b>📋 Bugun</b> da ko\'rsatadi. Band bo\'lsangiz <b>🚦 Holat</b> ni belgilang — eslatmalar jim turadi.',
        'careful' => '<i>Ikki xonali son summa deb olinmaydi: “ertalab 8 da yugurish” pul emas, reja.</i>',
    ],

    'btn' => [
        'today' => '📋 Bugun',
        'tomorrow' => '📅 Ertaga',
        'prev' => '◀️ Oldingi',
        'next' => 'Keyingi ▶️',
        'stats' => '📊 Statistika',
        'status' => '🚦 Holat',
        'refresh' => '🔄 Yangilash',
        'back' => '◀️ Orqaga',
        'done' => '✅ Bajarildi',
        'not_done' => '❌ Bajarilmadi',
        'later' => '⏳ Keyinroq',
        'free_again' => "🏁 Bo'shadim",
        'minutes' => '⏱ +:count daq',
        'hour' => '🕐 +1 soat',
        'evening' => '🌙 Kechqurun',
        'rest_of_day' => '🌇 Kun oxirigacha',
        'money' => '💰 Pul',
        'week' => '🗓 Shu hafta',
        'month' => '📆 Shu oy',
        'recent' => '🧾 Oxirgi yozuvlar',
        'undo' => '🗑 O\'chirish',
        'skip' => "⏭ O'tkazib yuborish",
        'change_category' => '🏷 Turkumni almashtirish',
        'all_categories' => '🗂 Barcha turkumlar',
        'add_expense' => '➕ Xarajat',
        'add_income' => '➕ Kirim',
        'today_money' => '📅 Bugungi hisob',
        'time' => '⏱ Vaqt',
        'add_time' => '➕ Vaqt yozish',
        'act_today' => '📅 Bugungi vaqt',
        'help' => '❓ Yordam',
        'language' => '🌐 Til',
        'home' => '🏠 Bosh sahifa',
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
        'updated' => '<i>🕐 Yangilangan: :time</i>',
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
        'week_so_far' => 'Shu hafta: <b>:amount</b>',
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
        'week_title' => '📅 <b>Shu hafta</b>',
        'recent_title' => '🧾 <b>Oxirgi yozuvlar</b>',
        'empty_recent' => "Hali bironta yozuv yo'q.",
        'empty_week' => "Bu hafta hali hech narsa yozilmadi.",
        'entries' => ':count ta yozuv',
        'pace' => 'Shu tezlikda oy oxirida: <b>:amount</b>',
        'uncategorised' => 'Turkumsiz',
        'pick_category' => 'Qaysi turkumga qo\'yay?',
        'note_line' => '<i>:note</i>',
        'category_changed' => '🏷 Turkum almashtirildi.',
        'how_to_add' => '<i>Tugmadan yozing, yoki bir qatorda: <code>ovqat 25000</code></i>',
        'pick_for_expense' => 'Nimaga sarfladingiz?',
        'pick_for_income' => 'Kirim nimadan?',
        'ask_amount' => "<b>:category</b> — qancha?\n\nFaqat raqamni yozing: <code>25000</code>, <code>12k</code> yoki <code>350 ming</code>.",
        'day_title' => '📅 <b>Bugun</b>',
        'empty_day' => 'Bugun hali hech narsa yozilmadi.',
    ],

    'act' => [
        'title' => '⏱ <b>Vaqt</b>',
        'today' => 'Bugun: <b>:duration</b>',
        'week_so_far' => 'Shu hafta: <b>:duration</b>',
        'month_so_far' => 'Shu oy: <b>:duration</b>',
        'covered' => 'Kunning <b>:percent%</b> i yozib olingan',
        'covered_period' => 'Davrning <b>:percent%</b> i yozib olingan',
        'average' => 'Kuniga o\'rtacha: <b>:duration</b>',
        'by_category' => 'Nimaga ketdi:',
        'saved' => '✅ <b>:duration</b> · :category',
        'saved_uncategorised' => '✅ <b>:duration</b> yozib olindi.',
        'ask_category' => 'Nima qildingiz?',
        'pick_for_new' => 'Nimaga vaqt ketdi?',
        'ask_duration' => "<b>:category</b> — qancha vaqt?\n\nMasalan: <code>2 soat</code>, <code>45 daqiqa</code> yoki <code>1 soat 30 daq</code>.",
        'how_to_add' => '<i>Tugmadan yozing, yoki bir qatorda: <code>8 soat uxladim</code></i>',
        'day_title' => '📅 <b>Bugungi vaqt</b>',
        'week_title' => '🗓 <b>Shu hafta</b>',
        'month_title' => '📆 <b>:month</b>',
        'empty_day' => 'Bugun hali vaqt yozilmadi.',
        'empty_week' => 'Bu hafta hali vaqt yozilmadi.',
        'empty_month' => 'Bu oyda hali vaqt yozilmadi.',
        'recent_title' => '🧾 <b>Oxirgi vaqt yozuvlari</b>',
        'empty_recent' => 'Hali bironta vaqt yozuvi yo\'q.',
        'entries' => ':count ta yozuv',
        'undone' => '🗑 O\'chirildi: :duration · :category',
        'nothing_to_undo' => 'O\'chiradigan vaqt yozuvim qolmadi.',
        'learned' => 'Yodda tutdim — bundan keyin “:word” ni :category deb o\'qiyman.',
        'targets' => 'Maqsadlar:',
        'target_line' => ':category — <b>:spent</b> / :target',
        'balance' => 'Foydali <b>:good</b> · behuda <b>:bad</b>',
        'from_status' => 'holatdan',
        'uncategorised' => 'Turkumsiz',
        'pick_category' => 'Qaysi turkumga qo\'yay?',
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

    /*
    | What Telegram itself shows about the bot — the name on the chat header,
    | the text on an empty chat, and the "/" menu. None of it lives in the
    | repository until `telegram:profile` pushes it, so this is the source.
    | Plain text only: Telegram does not read HTML in a bot description.
    */
    'profile' => [
        'short' => "Rejalar, xarajatlar va eslatmalar — hammasi bitta joyda.",
        'description' => "Iqbolshohning shaxsiy boti: rejalar, xarajatlar va eslatmalar bir joyda.

Rejani vaqtida eslataman, xarajatni esa bir qator matndan tushunaman: \"ovqat 25000\" deb yozsangiz, yozib qo'yaman. Kun oxirida nima sarflanganini o'zim so'rayman.

Bot yopiq: faqat egasi bilan gaplashadi.",
    ],

    'cmd' => [
        'menu' => 'Bosh sahifa',
        'today' => 'Bugungi rejalar',
        'tomorrow' => 'Ertangi rejalar',
        'status' => "Bandman yoki bo'shman deyish",
        'stats' => "Hafta qanday o'tdi",
        'money' => 'Pul: bugun va shu oy',
        'language' => 'Tilni almashtirish',
        'help' => 'Bot qanday ishlaydi',
        'time' => 'Vaqt: kun, hafta, oy',
    ],
];
