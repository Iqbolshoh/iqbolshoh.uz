<?php

namespace App\Services;

use App\Enums\PaymentMethod;
use App\Enums\TransactionKind;
use App\Enums\TransactionSource;
use App\Models\FinanceCategory;
use App\Models\FinanceSetting;
use App\Models\Transaction;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

/**
 * Writing money down, and everything that must happen at the same moment.
 *
 * The panel and the bot both come through here rather than calling
 * `Transaction::create()` themselves, because a row is never just a row: the
 * date has to be resolved on the owner's clock, and a limit that has just been
 * crossed has to be noticed while the owner is still holding their phone.
 */
class FinanceService
{
    /**
     * The categories every new account starts with.
     *
     * One flat list, deliberately fine-grained: "Transport" as a single bucket
     * answers nothing a month later, while Taxi, Public transport and Fuel
     * each say something the owner can act on. Nesting them under parents was
     * the other option and was rejected — a two-level picker is two taps on a
     * phone, and the whole point of this bot is that logging money is one line
     * of text and no taps at all.
     *
     * Keywords are what makes "taksi 12k" work without the owner configuring
     * anything, so each one carries the obvious words in all four languages.
     * They are a starting point, not a fence — the owner can edit every one,
     * and the bot adds to them whenever a guess is corrected.
     *
     * Two rules hold this list together, both enforced by tests:
     *
     * 1. Within one kind, no word may appear on two categories. Where they
     *    would overlap the longer phrase wins by design — "gaz quyish" is
     *    fuel, plain "gaz" is the utility bill — because the parser always
     *    prefers the longest match.
     *
     * 2. Across the two kinds a word may repeat ("qarz" is both a debt paid
     *    and a debt repaid to you). Expense is listed first and therefore wins
     *    when the text says nothing about direction, which is the commoner
     *    case; "+" or an income word picks the other one.
     *
     * @var array<int, array{key: string, kind: string, name: string, icon: string, color: string, keywords: string}>
     */
    private const DEFAULT_CATEGORIES = [
        ['key' => 'food', 'kind' => 'expense', 'name' => 'Groceries', 'icon' => '🛒', 'color' => '#F97316',
            'keywords' => "ovqat,oziq,do'kon,dokon,magazin,bozor,non,sut,go'sht,gosht,meva,sabzavot,tuxum,guruch,shakar,choy,produkti,продукты,магазин,базар,хлеб,молоко,мясо,овощи,фрукты,еда,groceries,food,market,озуқа,нон,шир,гушт,бозор,хӯрок,хурок,таом,eda"],
        ['key' => 'cafe', 'kind' => 'expense', 'name' => 'Cafes & restaurants', 'icon' => '☕', 'color' => '#EA580C',
            'keywords' => "kafe,kofe,restoran,choyxona,fastfud,osh,somsa,lagmon,shashlik,kabob,burger,pitsa,shaurma,nonushta,tushlik,kechkilik,кафе,кофе,ресторан,чайхана,фастфуд,пицца,бургер,шаурма,шашлык,обед,ужин,завтрак,cafe,restaurant,lunch,dinner,breakfast,coffee,қаҳвахона,тарабхона,қаҳва,obed,uzhin,zavtrak,tamaddi,kechki"],
        ['key' => 'taxi', 'kind' => 'expense', 'name' => 'Taxi', 'icon' => '🚕', 'color' => '#0EA5E9',
            'keywords' => "taksi,taxi,yandex,такси,яндекс"],
        ['key' => 'transport', 'kind' => 'expense', 'name' => 'Public transport', 'icon' => '🚌', 'color' => '#0284C7',
            'keywords' => "avtobus,marshrutka,metro,tramvay,yo'l kira,yol kira,yulkira,poyezd,poezd,elektrichka,автобус,маршрутка,метро,трамвай,поезд,электричка,bus,transport,транспорт,нақлиёт,наклиёт"],
        ['key' => 'fuel', 'kind' => 'expense', 'name' => 'Fuel & car', 'icon' => '⛽', 'color' => '#64748B',
            'keywords' => "benzin,yoqilg'i,yoqilgi,metan,propan,zapravka,mashina,avtomobil,moyka,shina,parkovka,бензин,топливо,заправка,мойка,шина,парковка,автомобиль,машина,fuel,petrol,сӯзишворӣ,мошин"],
        ['key' => 'rent', 'kind' => 'expense', 'name' => 'Rent', 'icon' => '🏠', 'color' => '#8B5CF6',
            'keywords' => "ijara,arenda,kvartira,аренда,квартира,rent,иҷора,ичора,хона,uy ijarasi"],
        ['key' => 'utilities', 'kind' => 'expense', 'name' => 'Utilities', 'icon' => '💡', 'color' => '#A78BFA',
            'keywords' => "kommunal,svet,elektr,gaz,suv,issiqlik,chiqindi,коммуналка,свет,электричество,газ,вода,отопление,мусор,utilities,electricity,water,коммуналӣ,барқ,коммунал,bills"],
        ['key' => 'home', 'kind' => 'expense', 'name' => 'Home & household', 'icon' => '🛋', 'color' => '#7C3AED',
            'keywords' => "ro'zg'or,rozgor,mebel,idish,supurgi,kir yuvish,tozalash,sovun,shampun,salfetka,мебель,посуда,уборка,стирка,мыло,шампунь,бытовая,household,furniture,рӯзгор,асбоб"],
        ['key' => 'connection', 'kind' => 'expense', 'name' => 'Mobile & internet', 'icon' => '📱', 'color' => '#06B6D4',
            'keywords' => "internet,mobil,telefon,aloqa,tarif,ucell,beeline,uzmobile,mobiuz,humans,интернет,мобильный,телефон,связь,тариф,mobile,алоқа"],
        ['key' => 'subscriptions', 'kind' => 'expense', 'name' => 'Subscriptions', 'icon' => '🔁', 'color' => '#14B8A6',
            'keywords' => "obuna,podpiska,netflix,spotify,youtube,chatgpt,claude,figma,подписка,subscription,обуна"],
        ['key' => 'pharmacy', 'kind' => 'expense', 'name' => 'Pharmacy', 'icon' => '💊', 'color' => '#EF4444',
            'keywords' => "dori,dorixona,apteka,vitamin,tabletka,ukol,аптека,лекарство,таблетки,витамин,укол,pharmacy,medicine,дору,дорухона"],
        ['key' => 'doctor', 'kind' => 'expense', 'name' => 'Doctor & tests', 'icon' => '🏥', 'color' => '#DC2626',
            'keywords' => "shifokor,vrach,doktor,kasalxona,klinika,tahlil,analiz,tish,stomatolog,врач,больница,клиника,анализ,стоматолог,узи,doctor,clinic,hospital,dentist,духтур,беморхона,tibbiy,тиб"],
        ['key' => 'sport', 'kind' => 'expense', 'name' => 'Sport', 'icon' => '🏋', 'color' => '#F43F5E',
            'keywords' => "sport,sportzal,fitnes,basseyn,trenajyor,abonement,спорт,фитнес,бассейн,тренажёр,абонемент,gym,fitness,варзиш"],
        ['key' => 'clothes', 'kind' => 'expense', 'name' => 'Clothes & shoes', 'icon' => '👕', 'color' => '#EC4899',
            'keywords' => "kiyim,poyabzal,krossovka,kostyum,ko'ylak,koylak,shim,kurtka,paypoq,одежда,обувь,кроссовки,костюм,рубашка,куртка,носки,clothes,shoes,либос,пойафзол"],
        ['key' => 'beauty', 'kind' => 'expense', 'name' => 'Beauty & care', 'icon' => '💇', 'color' => '#DB2777',
            'keywords' => "soch,sartarosh,kosmetika,parfyum,atir,manikur,salon,парикмахер,стрижка,косметика,парфюм,маникюр,barber,beauty,ороишгар"],
        ['key' => 'education', 'kind' => 'expense', 'name' => 'Education & courses', 'icon' => '📚', 'color' => '#22C55E',
            'keywords' => "kitob,kurs,o'quv,oquv,ta'lim,talim,universitet,maktab,repetitor,seminar,книга,курс,обучение,учеба,университет,школа,репетитор,семинар,book,course,education,китоб,таҳсил,мактаб"],
        ['key' => 'kids', 'kind' => 'expense', 'name' => 'Kids', 'icon' => '🧸', 'color' => '#84CC16',
            'keywords' => "bola,o'yinchoq,oyinchoq,pampers,bog'cha,bogcha,детский,дети,игрушки,памперс,садик,kids,children,toys,кӯдак,бача"],
        ['key' => 'entertainment', 'kind' => 'expense', 'name' => 'Entertainment', 'icon' => '🎬', 'color' => '#A855F7',
            'keywords' => "kino,teatr,konsert,o'yin,oyin,dam olish,sayr,park,attraksion,bilyard,кино,театр,концерт,игра,отдых,парк,развлечение,movie,cinema,game,фароғат,бозӣ,истироҳат,истирохат,бози,fun"],
        ['key' => 'travel', 'kind' => 'expense', 'name' => 'Travel', 'icon' => '✈️', 'color' => '#6366F1',
            'keywords' => "sayohat,aviabilet,samolyot,mehmonxona,otel,viza,ekskursiya,путешествие,авиабилет,самолет,отель,гостиница,виза,travel,flight,hotel,сафар,меҳмонхона"],
        ['key' => 'gifts', 'kind' => 'expense', 'name' => 'Gifts & celebrations', 'icon' => '🎁', 'color' => '#F59E0B',
            'keywords' => "sovg'a,sovga,to'y,tuy,nikoh,tug'ilgan kun,bayram,gul,подарок,свадьба,день рождения,праздник,цветы,gift,wedding,birthday,тӯҳфа,тухфа,ҷашн,туй"],
        ['key' => 'charity', 'kind' => 'expense', 'name' => 'Charity & help', 'icon' => '🤲', 'color' => '#FBBF24',
            'keywords' => "xayriya,ehson,sadaqa,yordam,благотворительность,садака,помощь,пожертвование,charity,donation,хайрия,садақа,кумак"],
        ['key' => 'tech', 'kind' => 'expense', 'name' => 'Tech & gadgets', 'icon' => '💻', 'color' => '#3B82F6',
            'keywords' => "texnika,noutbuk,kompyuter,monitor,klaviatura,sichqoncha,quloqchin,zaryadnik,planshet,televizor,техника,ноутбук,компьютер,монитор,клавиатура,наушники,зарядка,планшет,телевизор,laptop,gadget,гаҷет,tech"],
        ['key' => 'work', 'kind' => 'expense', 'name' => 'Work expenses', 'icon' => '🧰', 'color' => '#2563EB',
            'keywords' => "server,domen,domain,hosting,xosting,litsenziya,reklama,kantselyariya,сервер,домен,хостинг,лицензия,реклама,канцелярия,ads,license,хидматрасон"],
        ['key' => 'taxes', 'kind' => 'expense', 'name' => 'Taxes & fees', 'icon' => '🧾', 'color' => '#475569',
            'keywords' => "soliq,yig'im,yigim,jarima,boj,poshlina,komissiya,налог,штраф,пошлина,сбор,комиссия,tax,fine,андоз,ҷарима"],
        ['key' => 'debt_payment', 'kind' => 'expense', 'name' => 'Debt & loan', 'icon' => '💳', 'color' => '#B91C1C',
            'keywords' => "qarz,kredit,nasiya,rassrochka,ipoteka,кредит,рассрочка,ипотека,долг,loan,credit,қарз"],
        ['key' => 'savings', 'kind' => 'expense', 'name' => 'Savings', 'icon' => '🐖', 'color' => '#10B981',
            'keywords' => "jamg'arma,jamgarma,omonat,depozit,tejash,накопления,сбережения,вклад,депозит,savings,deposit,пасандоз"],
        ['key' => 'pets', 'kind' => 'expense', 'name' => 'Pets', 'icon' => '🐈', 'color' => '#92400E',
            'keywords' => "mushuk,kuchuk,hayvon,veterinar,yem,корм,кошка,собака,ветеринар,питомец,pet,vet,гурба"],
        ['key' => 'other_expense', 'kind' => 'expense', 'name' => 'Other', 'icon' => '📦', 'color' => '#6B7280',
            'keywords' => "boshqa,другое,other,дигар"],

        ['key' => 'salary', 'kind' => 'income', 'name' => 'Salary', 'icon' => '💼', 'color' => '#22C55E',
            'keywords' => "oylik,maosh,ish haqi,avans,premiya,зарплата,оклад,аванс,премия,salary,wage,маош,музд,zarplata,zp"],
        ['key' => 'freelance', 'kind' => 'income', 'name' => 'Freelance & orders', 'icon' => '🧾', 'color' => '#0EA5E9',
            'keywords' => "buyurtma,loyiha,freelance,заказ,проект,фриланс,order,project,фармоиш,лоиҳа,лоиха"],
        ['key' => 'business', 'kind' => 'income', 'name' => 'Business income', 'icon' => '🏪', 'color' => '#14B8A6',
            'keywords' => "biznes,savdo,sotdim,tushum,бизнес,торговля,продал,выручка,business,sales,тиҷорат"],
        ['key' => 'investment', 'kind' => 'income', 'name' => 'Investments & interest', 'icon' => '📈', 'color' => '#8B5CF6',
            'keywords' => "investitsiya,foiz,dividend,aksiya,kripto,инвестиции,процент,дивиденд,акции,крипто,investment,interest,сармоя"],
        ['key' => 'rent_income', 'kind' => 'income', 'name' => 'Rental income', 'icon' => '🔑', 'color' => '#6366F1',
            'keywords' => "ijaradan,ijara puli,сдал,аренда доход,rental,иҷорадиҳӣ"],
        ['key' => 'gift_income', 'kind' => 'income', 'name' => 'Gifts received', 'icon' => '🎀', 'color' => '#F59E0B',
            'keywords' => "hadya,sovg'a,sovga,подарили,подарок,gift,тӯҳфа"],
        ['key' => 'debt_return', 'kind' => 'income', 'name' => 'Debt repaid to me', 'icon' => '↩️', 'color' => '#F43F5E',
            'keywords' => "qarz,qaytardi,вернули,возврат,repaid,қарз"],
        ['key' => 'other_income', 'kind' => 'income', 'name' => 'Other income', 'icon' => '➕', 'color' => '#64748B',
            'keywords' => "boshqa kirim,другой доход,other income,даромади дигар"],
    ];

    /**
     * Keys that used to be seeded and are not any more.
     *
     * They are not deleted: their transactions are real history, and a month
     * total must not change because the list of buckets was reorganised. They
     * are switched off instead, which takes them out of the bot's picker and
     * out of anything new while leaving every old row readable.
     *
     * @var list<string>
     */
    private const RETIRED_KEYS = ['housing', 'health'];

    /**
     * Write one movement of money down.
     *
     * `date` and `time` default to the owner's wall clock, never the server's:
     * money spent at 22:00 in Samarkand belongs to that evening, and UTC is
     * still five hours behind on the previous day.
     *
     * @return array{transaction: Transaction, warning: ?array{category: ?FinanceCategory, used: float, total: int, limit: int}}
     */
    public function record(
        User $user,
        TransactionKind $kind,
        int $amount,
        ?FinanceCategory $category = null,
        ?string $note = null,
        PaymentMethod $method = PaymentMethod::Cash,
        TransactionSource $source = TransactionSource::Web,
        ?CarbonImmutable $at = null,
    ): array {
        $moment = $at ?? CarbonImmutable::now($user->timezone);

        $transaction = Transaction::query()->create([
            'user_id' => $user->id,
            'category_id' => $category?->id,
            'kind' => $kind->value,
            'amount' => $amount,
            'date' => $moment->toDateString(),
            'time' => $moment->format('H:i:s'),
            'note' => $note,
            'method' => $method->value,
            'source' => $source->value,
        ]);

        return [
            'transaction' => $transaction,
            'warning' => $this->warningFor($user, $transaction),
        ];
    }

    /**
     * The most recent row this source added, for "undo".
     *
     * Scoped to the source on purpose: the bot's undo must never reach into a
     * row typed in the admin panel, where the owner can see and delete it.
     */
    public function lastFrom(User $user, TransactionSource $source): ?Transaction
    {
        return Transaction::query()
            ->where('user_id', $user->id)
            ->where('source', $source->value)
            ->with('category')
            ->latest('id')
            ->first();
    }

    /** @return Collection<int, FinanceCategory> */
    public function categories(User $user, ?TransactionKind $kind = null): Collection
    {
        return FinanceCategory::query()
            ->where('user_id', $user->id)
            ->active()
            ->when($kind !== null, fn ($query) => $query->ofKind($kind))
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    /**
     * The categories for one kind, the ones this person actually uses first.
     *
     * The seeded order is a guess by the installer; the usage count is the
     * truth about this particular pocket. Ties fall back to the seeded order
     * so a fresh account still reads sensibly, and so the list does not
     * reshuffle itself between two taps.
     *
     * @return Collection<int, FinanceCategory>
     */
    public function categoriesByUse(User $user, TransactionKind $kind): Collection
    {
        return FinanceCategory::query()
            ->where('user_id', $user->id)
            ->active()
            ->ofKind($kind)
            ->withCount('transactions')
            ->orderByDesc('transactions_count')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    /**
     * Bring an account's seeded categories in line with the list above.
     *
     * Safe to run as often as you like, and it has to be: the default list
     * grows, and an account seeded a month ago must be able to catch up
     * without losing anything it has collected since.
     *
     * What it does, and deliberately does not do:
     *
     * - A missing key is created.
     * - A seeded category's keywords are reconciled: the current defaults for
     *   its key, plus every word the account has that no default claims —
     *   which is exactly the set the bot learned from corrections. A word that
     *   has since moved to another category is dropped, so "kafe" stops
     *   answering for Groceries the moment Cafés exists.
     * - Its icon and colour are refreshed, because those are the repo's to
     *   choose and nobody edits them by hand.
     * - Its NAME is left alone. The bot shows a seeded category through the
     *   translation files anyway, so the stored name is only a fallback, and
     *   overwriting it would undo a rename made in the panel.
     * - A key that is no longer a default is switched off, never deleted. See
     *   RETIRED_KEYS.
     *
     * @return array{created: int, updated: int, retired: int}
     */
    public function syncDefaults(User $user): array
    {
        $owned = $this->keywordOwners();

        $existing = FinanceCategory::query()
            ->where('user_id', $user->id)
            ->whereNotNull('key')
            ->get()
            ->keyBy('key');

        $created = 0;
        $updated = 0;

        foreach (self::DEFAULT_CATEGORIES as $index => $definition) {
            $category = $existing->get($definition['key']);

            if ($category === null) {
                FinanceCategory::query()->create($definition + [
                    'user_id' => $user->id,
                    'sort_order' => $index,
                    'is_active' => true,
                ]);

                $created++;

                continue;
            }

            $changes = array_filter([
                'keywords' => $this->reconcileKeywords($category, $definition, $owned),
                'icon' => $definition['icon'],
                'color' => $definition['color'],
                'sort_order' => $index,
            ], fn ($value, string $field): bool => $category->{$field} !== $value, ARRAY_FILTER_USE_BOTH);

            if ($changes !== []) {
                $category->update($changes);
                $updated++;
            }
        }

        $retired = FinanceCategory::query()
            ->where('user_id', $user->id)
            ->whereIn('key', self::RETIRED_KEYS)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        FinanceSetting::forUser($user->id);

        return ['created' => $created, 'updated' => $updated, 'retired' => $retired];
    }

    /**
     * Give an account the starter categories, once.
     *
     * Kept as the name the rest of the app calls, and now just the number of
     * categories the sync had to create — which is what every caller wanted
     * from it in the first place.
     */
    public function ensureDefaults(User $user): int
    {
        return $this->syncDefaults($user)['created'];
    }

    /**
     * Which default category each default keyword belongs to, per kind.
     *
     * Built once per sync so reconciling one category can tell "this word is
     * mine", "this word is another default's" and "nobody claims this word,
     * so the owner or the bot put it there" apart.
     *
     * @return array<string, array<string, string>>  kind => word => key
     */
    private function keywordOwners(): array
    {
        $owners = [];

        foreach (self::DEFAULT_CATEGORIES as $definition) {
            foreach ($this->words($definition['keywords']) as $word) {
                $owners[$definition['kind']][$word] = $definition['key'];
            }
        }

        return $owners;
    }

    /**
     * @param  array{key: string, kind: string, keywords: string}  $definition
     * @param  array<string, array<string, string>>  $owned
     */
    private function reconcileKeywords(FinanceCategory $category, array $definition, array $owned): string
    {
        $defaults = $this->words($definition['keywords']);

        $learned = array_filter(
            $this->words((string) $category->keywords),
            fn (string $word): bool => ($owned[$definition['kind']][$word] ?? $definition['key']) === $definition['key']
        );

        return implode(',', array_unique([...$defaults, ...$learned]));
    }

    /** @return list<string> */
    private function words(string $keywords): array
    {
        return array_values(array_filter(array_map(
            fn (string $word): string => trim(mb_strtolower($word)),
            explode(',', $keywords)
        )));
    }

    /**
     * Whether this row pushed its category past the warning threshold.
     *
     * Only the crossing is reported, not every row above it: a category that
     * is already over its limit would otherwise complain about every coffee
     * for the rest of the month, and a warning that arrives every time is one
     * nobody reads.
     */
    private function warningFor(User $user, Transaction $transaction): ?array
    {
        if ($transaction->kind !== TransactionKind::Expense || $transaction->category_id === null) {
            return null;
        }

        $category = $transaction->category;

        if ($category === null || ! $category->monthly_limit) {
            return null;
        }

        $month = CarbonImmutable::parse($transaction->date, $user->timezone);

        $total = (int) Transaction::query()
            ->where('user_id', $user->id)
            ->where('category_id', $category->id)
            ->ofKind(TransactionKind::Expense)
            ->between($month->startOfMonth(), $month->endOfMonth())
            ->sum('amount');

        $threshold = FinanceSetting::forUser($user->id)->warn_at_percent;
        $before = $total - $transaction->amount;

        $share = fn (int $sum): float => $category->monthly_limit > 0
            ? $sum / $category->monthly_limit * 100
            : 0.0;

        if ($share($before) >= $threshold || $share($total) < $threshold) {
            return null;
        }

        return [
            'category' => $category,
            'used' => round($share($total), 1),
            'total' => $total,
            'limit' => (int) $category->monthly_limit,
        ];
    }
}
