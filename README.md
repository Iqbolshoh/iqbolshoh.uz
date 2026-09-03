# iqbolshoh.uz

Bitta domenda uchta narsa yashaydi:

1. **Portfolio sayti** — React SPA, kontenti bazadan olinadi.
2. **Admin panel** — Laravel Blade, sayt kontenti + shaxsiy reja va pul hisobi.
3. **Telegram bot** — o'sha reja va pulning cho'ntakdagi ko'rinishi.

## Tuzilma

```
.
├── src/            React (TypeScript, Vite, Tailwind) — faqat ochiq sayt
├── public/         Vite statik fayllari (rasmlar, favicon, CV)
├── dist/           Build natijasi — nginx shu papkani ko'rsatadi (git'ga kirmaydi)
└── backend/        Laravel 13
    ├── app/Http/Controllers/Api/     ContentController, ContactController
    ├── app/Http/Controllers/Admin/   panel: kontent, reja, moliya
    ├── app/Services/                 PlanService, FinanceService, TelegramBot, FinanceBot
    ├── app/Console/Commands/         eslatma, kunlik xulosa, moliya hisoboti
    ├── lang/{uz,ru,en,tj}/           botning to'rt tili (panel — faqat inglizcha)
    └── database/seeders/             ContentSeeder, RolePermissionSeeder
```

## Ochiq API

| Yo'l | Usul | Vazifa |
|---|---|---|
| `/api/content` | GET | Butun sayt kontenti bitta so'rovda (60 s kesh) |
| `/api/projects` | GET | Loyihalar |
| `/api/services` | GET | Xizmatlar |
| `/api/contact` | POST | Aloqa formasi (daqiqasiga 5 ta) |
| `/api/service-order` | POST | Xizmat buyurtmasi (daqiqasiga 5 ta) |

Ko'p tilli matnlar JSON ustunda `{"en":…,"uz":…,"ru":…,"tj":…}` ko'rinishida
saqlanadi. Ikonkalar bazada matn sifatida (`"Award"`), frontend ularni
`src/lib/icons.ts` orqali lucide komponentiga aylantiradi — yangi ikonka
qo'shsangiz, o'sha jadvalga ham qo'shing, aks holda standarti chiqadi.

## Admin panel

`/admin` — kirish `AuthController` orqali, huquqlar `RolePermissionSeeder`
dagi bitta xaritadan tarqaladi (panel menyusi ham, rol formasi ham o'sha
xaritadan chiziladi).

- **Kontent** — loyihalar, xizmatlar, sozlamalar, kelgan xabarlar.
- **Reja** — rejalar, maqsadlar, kalendar, tahlil, prognoz, bildirishnomalar.
- **Moliya** — kirim/chiqim daftari, kategoriyalar, byudjet va ogohlantirishlar.

## Telegram bot

Bot `@ilhomjonov_777_bot`, webhook `POST /telegram/webhook`,
`TELEGRAM_WEBHOOK_SECRET` sarlavhasisiz 403 qaytaradi.

Buyruqlar: `/menu`, `/today`, `/tomorrow`, `/status`, `/stats`, `/money`
(`/pul`), `/language` (`/til`). Buyruq bo'lmagan har qanday matn avval pul deb
o'qiladi: `ovqat 25000` bitta xarajat qatori bo'lib yoziladi, summa topilmasa
bot menyuni ko'rsatadi.

Til hisob bo'yicha saqlanadi (`telegram_accounts.locale`) va har bir yangilanish
uchun alohida o'rnatiladi — queue worker uzoq yashaydigan jarayon, oldingi
suhbatdan qolgan til keyingisiga javob berib qo'ymasligi kerak.

```bash
php artisan telegram:webhook set      # webhook o'rnatish (info / delete ham bor)
php artisan telegram:profile          # nom, tavsif va "/" menyusi — hamma tilda
php artisan telegram:profile --commands   # faqat "/" menyusi
php artisan telegram:profile --dry-run    # nima ketishini ko'rsatadi, yubormaydi
```

Botning nomi, tavsifi, avatari va buyruqlar menyusi Telegram tomonida
saqlanadi — kodda emas. Shuning uchun ular repozitoriyda turadi va faqat
`telegram:profile` orqali yetkaziladi:

- buyruqlar va tavsiflar — `lang/<til>/bot.php` (to'rt tilda);
- nom — `TELEGRAM_BOT_NAME` (hamma tilda bitta, shuning uchun bir marta
  yuboriladi — `setMyName` cheklovi qattiq);
- avatar — `backend/resources/telegram/avatar.png` (kvadrat, saytdagi logodan).

Ikkita qoida: standart (default) doira ham yuborilishi shart, aks holda
ro'yxati yo'q tildagi mijoz hech nima ko'rmaydi; va Telegram tojikchani `tg`
deb yuritadi, bu yerdagi `tj` emas.

## Rejalashtirilgan ishlar

Hech biri `iqbolshoh-schedule` supervisor jarayonisiz ishlamaydi — eslatma
kelmasa, birinchi shu tekshiriladi.

| Buyruq | Qachon |
|---|---|
| `plans:remind` | har daqiqa — vaqti kelgan reja eslatmasi |
| `plans:daily-summary` | har soat, har hisob o'z soatida |
| `finance:prompt` | har soat — "bugun nimaga ketdi?" |
| `finance:report` | har soat — haftalik/oylik moliya hisoboti |

Bot jim turishi odatiy holat: ochiq reja bo'lmasa `plans:remind` gapirmaydi,
kun bo'sh bo'lsa `plans:daily-summary` ham. Sabab qidirishdan oldin ochiq
rejalar sonini sanang.

## O'rnatish

```bash
# Backend
cd backend
composer install
cp .env.example .env        # DB va TELEGRAM_* qiymatlarini to'ldiring
php artisan key:generate
php artisan migrate --seed

# Frontend
npm install
npm run build               # dist/ hosil bo'ladi
```

## Deploy

`dist/` git'ga kirmaydi, shuning uchun serverda build qilish shart:

```bash
git pull
npm ci && npm run build
cd backend && composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan route:cache
php artisan queue:restart          # worker eski kod va tarjimani ushlab turadi
```

nginx `dist/` ni statik tarqatadi, `/api`, `/admin`, `/telegram` va `/up` ni
`backend/public` ga uzatadi.

## Testlar

```bash
cd backend
LOG_CHANNEL=null php artisan test    # aks holda jonli log to'lib ketadi
```

Testlar sqlite `:memory:` da ishlaydi, lekin ularni **root** nomidan
yugurtirmang — keyin fayl egaligi buziladi.
