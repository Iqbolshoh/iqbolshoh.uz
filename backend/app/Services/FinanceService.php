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
     * Keywords are what makes "taksi 12k" work without the owner configuring
     * anything, so each one carries the obvious words in all four languages.
     * They are a starting point, not a fence — the owner can edit every one.
     *
     * @var array<int, array{key: string, kind: string, name: string, icon: string, color: string, keywords: string}>
     */
    private const DEFAULT_CATEGORIES = [
        ['key' => 'food', 'kind' => 'expense', 'name' => 'Food', 'icon' => '🍽', 'color' => '#F97316',
            'keywords' => "ovqat,tamaddi,nonushta,tushlik,kechki,food,lunch,dinner,breakfast,eda,еда,обед,ужин,завтрак,продукты,obed,uzhin,zavtrak,produkti,хӯрок,хурок,таом,kafe,кафе,restoran,ресторан,osh,non,нон"],
        ['key' => 'transport', 'kind' => 'expense', 'name' => 'Transport', 'icon' => '🚕', 'color' => '#0EA5E9',
            'keywords' => "yol kira,yo'l kira,yulkira,taksi,taxi,такси,avtobus,автобус,metro,метро,benzin,бензин,yoqilgi,transport,транспорт,naqliyot,нақлиёт,наклиёт,yandex"],
        ['key' => 'housing', 'kind' => 'expense', 'name' => 'Home & bills', 'icon' => '🏠', 'color' => '#8B5CF6',
            'keywords' => "ijara,kvartira,kommunal,svet,gaz,suv,uy,аренда,квартира,коммунал,свет,газ,вода,rent,bills,utilities,иҷора,ичора,хона"],
        ['key' => 'connection', 'kind' => 'expense', 'name' => 'Mobile & internet', 'icon' => '📱', 'color' => '#06B6D4',
            'keywords' => "internet,интернет,mobil,telefon,телефон,aloqa,связь,ucell,beeline,uzmobile,mobi,тариф,aloqa"],
        ['key' => 'health', 'kind' => 'expense', 'name' => 'Health', 'icon' => '💊', 'color' => '#EF4444',
            'keywords' => "dori,dorixona,shifokor,kasalxona,tibbiy,дори,дорухона,аптека,лекарство,врач,больница,health,doctor,pharmacy,дору,духтур,тиб"],
        ['key' => 'clothes', 'kind' => 'expense', 'name' => 'Clothes', 'icon' => '👕', 'color' => '#EC4899',
            'keywords' => "kiyim,poyabzal,krossovka,одежда,обувь,clothes,shoes,либос,пойафзол"],
        ['key' => 'education', 'kind' => 'expense', 'name' => 'Education', 'icon' => '📚', 'color' => '#22C55E',
            'keywords' => "kitob,kurs,oquv,o'quv,ta'lim,talim,книга,курс,обучение,учеба,book,course,education,китоб,таҳсил,тахсил"],
        ['key' => 'entertainment', 'kind' => 'expense', 'name' => 'Entertainment', 'icon' => '🎬', 'color' => '#A855F7',
            'keywords' => "kino,oyin,dam,sayr,кино,игра,отдых,развлечение,fun,game,movie,бозӣ,бози,истирохат"],
        ['key' => 'gifts', 'kind' => 'expense', 'name' => 'Gifts & help', 'icon' => '🎁', 'color' => '#F59E0B',
            'keywords' => "sovga,tuy,to'y,yordam,xayriya,подарок,свадьба,помощь,gift,help,тӯҳфа,тухфа,туй,кумак"],
        ['key' => 'tech', 'kind' => 'expense', 'name' => 'Tech & work', 'icon' => '💻', 'color' => '#3B82F6',
            'keywords' => "server,domen,domain,hosting,podpiska,obuna,подписка,сервер,домен,техника,noutbuk,ноутбук,kompyuter,компьютер,texnika,tech"],
        ['key' => 'other_expense', 'kind' => 'expense', 'name' => 'Other', 'icon' => '📦', 'color' => '#6B7280',
            'keywords' => "boshqa,другое,other,дигар"],

        ['key' => 'salary', 'kind' => 'income', 'name' => 'Salary', 'icon' => '💼', 'color' => '#22C55E',
            'keywords' => "oylik,maosh,ish haqi,зарплата,оклад,zarplata,zp,salary,wage,маош,музд"],
        ['key' => 'freelance', 'kind' => 'income', 'name' => 'Freelance & orders', 'icon' => '🧾', 'color' => '#0EA5E9',
            'keywords' => "buyurtma,loyiha,freelance,фриланс,заказ,проект,order,project,фармоиш,лоиҳа,лоиха"],
        ['key' => 'other_income', 'kind' => 'income', 'name' => 'Other income', 'icon' => '➕', 'color' => '#14B8A6',
            'keywords' => "qarz,sovga,boshqa,долг,подарок,другое,other,қарз,карз,дигар"],
    ];

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
     * Give an account the starter categories, once.
     *
     * Matched on `key` so running it again after a new default is added tops
     * the account up instead of duplicating what is already there — and a
     * category the owner renamed keeps its new name.
     */
    public function ensureDefaults(User $user): int
    {
        $existing = FinanceCategory::query()
            ->where('user_id', $user->id)
            ->whereNotNull('key')
            ->pluck('key')
            ->all();

        $created = 0;

        foreach (self::DEFAULT_CATEGORIES as $index => $definition) {
            if (in_array($definition['key'], $existing, true)) {
                continue;
            }

            FinanceCategory::query()->create($definition + [
                'user_id' => $user->id,
                'sort_order' => $index,
                'is_active' => true,
            ]);

            $created++;
        }

        FinanceSetting::forUser($user->id);

        return $created;
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
