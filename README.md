# iqbolshoh.uz

React (frontend) + Laravel (API) asosidagi dinamik portfolio sayt.
Sayt kontenti — loyihalar, xizmatlar, blog, tajriba yo'li — bazadan olinadi
va Laravel seeder orqali to'ldiriladi.

## Tuzilma

```
.
├── src/            React (TypeScript, Vite, Tailwind)
├── public/         Vite statik fayllari (rasmlar, favicon, CV)
├── dist/           Build natijasi — nginx shu papkani ko'rsatadi (git'ga kirmaydi)
└── backend/        Laravel 13 API
    ├── app/Http/Controllers/Api/   ContentController, ContactController
    ├── database/seeders/           ContentSeeder — saytning barcha matni shu yerda
    └── routes/api.php
```

## API

| Yo'l | Usul | Vazifa |
|---|---|---|
| `/api/content` | GET | Butun sayt kontenti bitta so'rovda (60 s kesh) |
| `/api/projects` | GET | Loyihalar |
| `/api/services` | GET | Xizmatlar |
| `/api/blog` | GET | Blog ro'yxati |
| `/api/blog/{slug}` | GET | Bitta maqola |
| `/api/contact` | POST | Aloqa formasi |
| `/api/service-order` | POST | Xizmat buyurtmasi |

Ko'p tilli matnlar JSON ustunda `{"en":…,"uz":…,"ru":…,"tj":…}` ko'rinishida saqlanadi.
Ikonkalar bazada matn sifatida (`"Award"`), frontend ularni `src/lib/icons.ts`
orqali lucide komponentiga aylantiradi.

## Kontentni o'zgartirish

Matn, loyiha yoki xizmat qo'shish uchun `backend/database/seeders/ContentSeeder.php`
faylini tahrirlang va qayta seed qiling:

```bash
cd backend
php artisan migrate:fresh --seed   # DIQQAT: jadvallarni tozalaydi
php artisan cache:clear            # /api/content keshini yangilaydi
```

Yangi ikonka qo'shsangiz, uni `src/lib/icons.ts` jadvaliga ham qo'shing —
aks holda o'rniga standart ikonka chiqadi.

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
php artisan config:cache && php artisan route:cache
```

nginx `dist/` ni statik tarqatadi, `/api` va `/up` ni `backend/public` ga uzatadi.
