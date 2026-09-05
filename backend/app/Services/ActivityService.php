<?php

namespace App\Services;

use App\Enums\ActivitySource;
use App\Models\ActivityCategory;
use App\Models\ActivityEntry;
use App\Models\Interruption;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

/**
 * Writing down where the time went.
 *
 * The same shape as FinanceService, and for the same reason: the panel and the
 * bot both come through here rather than calling `ActivityEntry::create()`
 * themselves, because the date has to be resolved on the owner's clock and not
 * the server's.
 */
class ActivityService
{
    /**
     * A day, in minutes. The report's denominator: eight hours logged out of
     * this is the number that makes the rest of the list mean something.
     */
    public const DAY_MINUTES = 1440;

    /**
     * The activities every new account starts with.
     *
     * Chosen to cover a real day rather than to be exhaustive: sleep, work,
     * study and the road are most of most days, and the rest are the ones
     * worth seeing a weekly total for. `is_good` says which way the number
     * should be read — more study is progress, more scrolling is not — and it
     * is the only reason the report can say anything at all beyond the raw
     * hours.
     *
     * Keywords carry the obvious words in all four languages, and the same two
     * rules as the finance catalogue hold: no word may answer for two
     * activities, and every key must be named in every language. Both are
     * tested rather than trusted.
     *
     * @var array<int, array{key: string, name: string, icon: string, color: string, keywords: string, is_good: bool, daily_target_minutes: ?int}>
     */
    private const DEFAULT_CATEGORIES = [
        ['key' => 'sleep', 'name' => 'Sleep', 'icon' => '😴', 'color' => '#6366F1', 'is_good' => true, 'daily_target_minutes' => 480,
            'keywords' => "uyqu,uxladim,uxlash,mizgib,сон,спал,поспал,сна,sleep,slept,nap,хоб,хобидам"],
        ['key' => 'work', 'name' => 'Work', 'icon' => '💼', 'color' => '#2563EB', 'is_good' => true, 'daily_target_minutes' => 480,
            'keywords' => "ish,ishladim,kod,kodladim,dasturlash,loyiha ustida,работа,работал,код,программировал,work,worked,coding,кор,кор кардам"],
        ['key' => 'study', 'name' => 'Study', 'icon' => '📚', 'color' => '#22C55E', 'is_good' => true, 'daily_target_minutes' => 120,
            'keywords' => "dars,oqidim,o'qidim,darslik,kurs,mutolaa,kitob oqidim,учеба,урок,занимался,читал,курс,study,studied,lesson,reading,таҳсил,дарс,хондам"],
        ['key' => 'meeting', 'name' => 'Meetings', 'icon' => '🤝', 'color' => '#0EA5E9', 'is_good' => true, 'daily_target_minutes' => null,
            'keywords' => "uchrashuv,majlis,suhbat,qongiroq,встреча,созвон,совещание,meeting,call,вохӯрӣ,мулоқот"],
        ['key' => 'travel', 'name' => 'On the road', 'icon' => '🚗', 'color' => '#64748B', 'is_good' => false, 'daily_target_minutes' => null,
            'keywords' => "yolda,yo'lda,yol,safar,qatnov,дорога,в пути,ехал,поездка,commute,travel,road,дар роҳ,сафар"],
        ['key' => 'sport', 'name' => 'Sport', 'icon' => '🏋', 'color' => '#F43F5E', 'is_good' => true, 'daily_target_minutes' => 45,
            'keywords' => "sport,mashq,yugurdim,yugurish,zal,fitnes,cho'zilish,спорт,тренировка,бегал,зал,workout,gym,running,варзиш,машқ"],
        ['key' => 'meal', 'name' => 'Meals', 'icon' => '🍽', 'color' => '#F97316', 'is_good' => true, 'daily_target_minutes' => null,
            'keywords' => "ovqatlandim,tamaddi,nonushta qildim,еда,обедал,ужинал,завтракал,питание,meal,eating,lunch break,хӯрокхӯрӣ,хӯрдам"],
        ['key' => 'family', 'name' => 'Family & friends', 'icon' => '👨‍👩‍👧', 'color' => '#EC4899', 'is_good' => true, 'daily_target_minutes' => null,
            'keywords' => "oila,bolalar bilan,mehmon,dost,qarindosh,семья,с детьми,гости,друзья,family,friends,guests,оила,меҳмон,дӯстон"],
        ['key' => 'chores', 'name' => 'Chores & errands', 'icon' => '🧹', 'color' => '#A16207', 'is_good' => true, 'daily_target_minutes' => null,
            'keywords' => "yumush,tozalash,tozaladim,xarid,bozorga,ta'mir,быт,уборка,убирал,покупки,ремонт,chores,cleaning,errands,корҳои хона,тозакунӣ"],
        ['key' => 'rest', 'name' => 'Rest', 'icon' => '🛋', 'color' => '#14B8A6', 'is_good' => true, 'daily_target_minutes' => null,
            'keywords' => "dam,dam oldim,hordiq,sayr qildim,отдых,отдыхал,гулял,rest,rested,walk,истироҳат,дам гирифтам"],
        ['key' => 'entertainment', 'name' => 'Films & games', 'icon' => '🎬', 'color' => '#A855F7', 'is_good' => false, 'daily_target_minutes' => null,
            'keywords' => "kino,film,serial,o'yin,oyin,youtube,кино,фильм,сериал,игра,игры,movie,series,gaming,филм,бозӣ"],
        ['key' => 'scrolling', 'name' => 'Phone & feeds', 'icon' => '📱', 'color' => '#EF4444', 'is_good' => false, 'daily_target_minutes' => 60,
            'keywords' => "telefonda,ijtimoiy tarmoq,instagram,telegramda,tiktok,lenta,телефон,соцсети,инстаграм,тикток,лента,scrolling,social,feeds,шабакаҳо"],
        ['key' => 'prayer', 'name' => 'Prayer', 'icon' => '🕌', 'color' => '#0D9488', 'is_good' => true, 'daily_target_minutes' => null,
            'keywords' => "namoz,ibodat,qur'on,quron,намаз,молитва,коран,prayer,namaz,намоз,ибодат"],
        ['key' => 'other_activity', 'name' => 'Other', 'icon' => '⌛', 'color' => '#6B7280', 'is_good' => true, 'daily_target_minutes' => null,
            'keywords' => "boshqa ish,другое время,other time,вақти дигар"],
    ];

    /**
     * What an interruption turns into once it is over.
     *
     * The status screen already asks what the owner is doing and for how long,
     * so those hours are known without anybody typing them twice. Anything not
     * listed here has no honest activity to become and is left alone.
     *
     * @var array<string, string>
     */
    private const FROM_INTERRUPTION = [
        'meeting' => 'meeting',
        'travel' => 'travel',
        'guest' => 'family',
        'class' => 'study',
        'work' => 'work',
        'rest' => 'rest',
    ];

    /** Write one stretch of time down. */
    public function record(
        User $user,
        int $minutes,
        ?ActivityCategory $category = null,
        ?string $note = null,
        ActivitySource $source = ActivitySource::Web,
        ?CarbonImmutable $at = null,
        ?Interruption $interruption = null,
    ): ActivityEntry {
        $moment = $at ?? CarbonImmutable::now($user->timezone);

        return ActivityEntry::query()->create([
            'user_id' => $user->id,
            'category_id' => $category?->id,
            'minutes' => $minutes,
            'date' => $moment->toDateString(),
            'note' => $note,
            'source' => $source->value,
            'interruption_id' => $interruption?->id,
        ]);
    }

    /**
     * Turn a finished interruption into the hours it actually was.
     *
     * Deliberately idempotent through the unique key on `interruption_id`: the
     * scheduler closes interruptions on a timer and the owner can close the
     * same one by hand a second later, and two calls must not become two
     * entries. A period under a minute is skipped — it is noise, not a
     * stretch of the day.
     */
    public function recordInterruption(Interruption $interruption): ?ActivityEntry
    {
        $key = self::FROM_INTERRUPTION[$interruption->type->value ?? (string) $interruption->type] ?? null;

        if ($key === null || ! $interruption->duration_minutes || $interruption->duration_minutes < 1) {
            return null;
        }

        if (ActivityEntry::query()->where('interruption_id', $interruption->id)->exists()) {
            return null;
        }

        $user = $interruption->user;

        $category = ActivityCategory::query()
            ->where('user_id', $user->id)
            ->where('key', $key)
            ->first();

        return $this->record(
            user: $user,
            minutes: (int) $interruption->duration_minutes,
            category: $category,
            note: $interruption->title,
            source: ActivitySource::Status,
            at: CarbonImmutable::parse($interruption->started_at)->setTimezone($user->timezone),
            interruption: $interruption,
        );
    }

    /**
     * The most recent row this source added, for "undo".
     *
     * Scoped to the source: undo must never withdraw an entry the bot derived
     * from a status, which the owner did not type and would not expect to
     * disappear.
     */
    public function lastFrom(User $user, ActivitySource $source): ?ActivityEntry
    {
        return ActivityEntry::query()
            ->where('user_id', $user->id)
            ->where('source', $source->value)
            ->with('category')
            ->latest('id')
            ->first();
    }

    /** @return Collection<int, ActivityCategory> */
    public function categories(User $user): Collection
    {
        return ActivityCategory::query()
            ->where('user_id', $user->id)
            ->active()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    /**
     * The activities this person actually logs, first.
     *
     * The seeded order is the installer's guess; the count is the truth about
     * this particular day. Ties fall back to the seeded order so the list does
     * not reshuffle itself between two taps.
     *
     * @return Collection<int, ActivityCategory>
     */
    public function categoriesByUse(User $user): Collection
    {
        return ActivityCategory::query()
            ->where('user_id', $user->id)
            ->active()
            ->withCount('entries')
            ->orderByDesc('entries_count')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    /**
     * Bring an account's seeded activities in line with the list above.
     *
     * Same contract as FinanceService::syncDefaults, down to the reasoning:
     * missing keys are created, keywords are reconciled so a word that has
     * moved stops answering for its old owner while everything the bot learned
     * survives, and the stored name is left alone because the bot reads it out
     * of the translation files anyway.
     *
     * @return array{created: int, updated: int}
     */
    public function syncDefaults(User $user): array
    {
        $owned = [];

        foreach (self::DEFAULT_CATEGORIES as $definition) {
            foreach ($this->words($definition['keywords']) as $word) {
                $owned[$word] = $definition['key'];
            }
        }

        $existing = ActivityCategory::query()
            ->where('user_id', $user->id)
            ->whereNotNull('key')
            ->get()
            ->keyBy('key');

        $created = 0;
        $updated = 0;

        foreach (self::DEFAULT_CATEGORIES as $index => $definition) {
            $category = $existing->get($definition['key']);

            if ($category === null) {
                ActivityCategory::query()->create($definition + [
                    'user_id' => $user->id,
                    'sort_order' => $index,
                    'is_active' => true,
                ]);

                $created++;

                continue;
            }

            $defaults = $this->words($definition['keywords']);

            $learned = array_filter(
                $this->words((string) $category->keywords),
                fn (string $word): bool => ($owned[$word] ?? $definition['key']) === $definition['key']
            );

            $changes = array_filter([
                'keywords' => implode(',', array_unique([...$defaults, ...$learned])),
                'icon' => $definition['icon'],
                'color' => $definition['color'],
                'sort_order' => $index,
            ], fn ($value, string $field): bool => $category->{$field} !== $value, ARRAY_FILTER_USE_BOTH);

            if ($changes !== []) {
                $category->update($changes);
                $updated++;
            }
        }

        return ['created' => $created, 'updated' => $updated];
    }

    /** Give an account the starter activities, once. */
    public function ensureDefaults(User $user): int
    {
        return $this->syncDefaults($user)['created'];
    }

    /** @return list<string> */
    private function words(string $keywords): array
    {
        return array_values(array_filter(array_map(
            fn (string $word): string => trim(mb_strtolower($word)),
            explode(',', $keywords)
        )));
    }
}
