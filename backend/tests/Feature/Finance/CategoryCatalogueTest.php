<?php

namespace Tests\Feature\Finance;

use App\Enums\TransactionKind;
use App\Models\FinanceCategory;
use App\Models\Transaction;
use App\Models\User;
use App\Services\FinanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Lang;
use ReflectionClass;
use Tests\TestCase;

/**
 * The list of buckets money can fall into, and how an existing account is
 * brought up to date when that list changes.
 *
 * The catalogue is fine-grained on purpose — Taxi and Public transport and
 * Fuel rather than one "Transport" — and that only pays off while two rules
 * hold. Both are checked here rather than by reading the list, because the
 * list is long enough that reading it proves nothing.
 */
class CategoryCatalogueTest extends TestCase
{
    use RefreshDatabase;

    private const LOCALES = ['uz', 'ru', 'en', 'tj'];

    private FinanceService $finance;

    protected function setUp(): void
    {
        parent::setUp();

        $this->finance = app(FinanceService::class);
    }

    /**
     * A category with no line in a language shows its English fallback inside
     * an Uzbek sentence — which reads as a bug and is invisible in English.
     */
    public function test_every_default_category_is_named_in_every_language(): void
    {
        foreach (self::LOCALES as $locale) {
            $named = array_keys(Lang::get('finance.categories', [], $locale));

            $this->assertSame(
                [],
                array_diff($this->defaultKeys(), $named),
                "lang/{$locale}/finance.php does not name every default category"
            );

            $this->assertSame(
                [],
                array_diff($named, $this->defaultKeys()),
                "lang/{$locale}/finance.php names a category that is not seeded"
            );
        }
    }

    /**
     * Within one kind, a word may answer for exactly one category.
     *
     * Two categories claiming "kafe" is not a tie the parser can break: it
     * keeps the first longest match, so the winner is whichever happens to be
     * seeded earlier — and the loser is unreachable from the bot forever,
     * while every test still passes.
     */
    public function test_no_word_answers_for_two_categories_of_the_same_kind(): void
    {
        $seen = [];
        $clashes = [];

        foreach ($this->defaults() as $definition) {
            foreach (explode(',', $definition['keywords']) as $word) {
                $word = trim(mb_strtolower($word));
                $slot = $definition['kind'] . '/' . $word;

                if (isset($seen[$slot])) {
                    $clashes[] = "“{$word}” is claimed by both {$seen[$slot]} and {$definition['key']}";
                }

                $seen[$slot] = $definition['key'];
            }
        }

        $this->assertSame([], $clashes);
    }

    /** A word may repeat across the two kinds — that is how "qarz" works both ways. */
    public function test_a_word_may_mean_one_thing_paid_and_another_received(): void
    {
        $user = User::factory()->create();
        $this->finance->syncDefaults($user);

        $categories = $this->finance->categories($user);
        $parser = app(\App\Services\MoneyTextParser::class);

        $this->assertSame('debt_payment', $parser->parse('qarz 500000', $categories)['category']->key);
        $this->assertSame('debt_return', $parser->parse('+qarz 500000', $categories)['category']->key);
    }

    public function test_sync_creates_what_is_missing_and_reports_it(): void
    {
        $user = User::factory()->create();

        $first = $this->finance->syncDefaults($user);

        $this->assertSame(count($this->defaults()), $first['created']);

        // Running it again is the normal case — a deploy, a panel button — and
        // must be a no-op rather than a second set of categories.
        $second = $this->finance->syncDefaults($user);

        $this->assertSame(0, $second['created']);
        $this->assertSame(0, $second['updated']);
        $this->assertSame(
            count($this->defaults()),
            FinanceCategory::query()->where('user_id', $user->id)->count()
        );
    }

    /**
     * A keyword that has moved to a new category is taken off the old one.
     *
     * This is what makes splitting a bucket actually work on an account that
     * already exists: without it "kafe" would still answer for Groceries, and
     * the new Cafés category would never see a single row.
     */
    public function test_sync_takes_back_a_keyword_that_now_belongs_elsewhere(): void
    {
        $user = User::factory()->create();
        $this->finance->syncDefaults($user);

        $food = FinanceCategory::query()->where('user_id', $user->id)->where('key', 'food')->firstOrFail();

        // The shape an account seeded before the split is in.
        $food->update(['keywords' => $food->keywords . ',kafe,restoran']);

        $this->finance->syncDefaults($user);

        $this->assertNotContains('kafe', $food->fresh()->matchWords());
        $this->assertContains('kafe', FinanceCategory::query()
            ->where('user_id', $user->id)->where('key', 'cafe')->firstOrFail()->matchWords());
    }

    /**
     * A word the bot learned from a correction is nobody's default, and it
     * survives every sync. Losing it would mean the owner has to correct the
     * same word again after each deploy.
     */
    public function test_sync_keeps_a_word_the_bot_learned(): void
    {
        $user = User::factory()->create();
        $this->finance->syncDefaults($user);

        $cafe = FinanceCategory::query()->where('user_id', $user->id)->where('key', 'cafe')->firstOrFail();
        $cafe->update(['keywords' => $cafe->keywords . ',qandolatxona']);

        $this->finance->syncDefaults($user);

        $this->assertContains('qandolatxona', $cafe->fresh()->matchWords());
    }

    /**
     * A bucket that has been split up is switched off, never deleted: its rows
     * are real history, and a month's total must not change because the list
     * of categories was reorganised.
     */
    public function test_a_retired_category_is_switched_off_and_keeps_its_rows(): void
    {
        $user = User::factory()->create();

        $health = FinanceCategory::query()->create([
            'user_id' => $user->id,
            'kind' => 'expense',
            'key' => 'health',
            'name' => 'Health',
            'is_active' => true,
        ]);

        $transaction = Transaction::query()->create([
            'user_id' => $user->id,
            'category_id' => $health->id,
            'kind' => 'expense',
            'amount' => 87500,
            'date' => now()->toDateString(),
        ]);

        $result = $this->finance->syncDefaults($user);

        $this->assertSame(1, $result['retired']);
        $this->assertFalse($health->fresh()->is_active);
        $this->assertSame($health->id, $transaction->fresh()->category_id);

        // Off means out of the bot's reach, not out of the database.
        $this->assertFalse(
            $this->finance->categoriesByUse($user, TransactionKind::Expense)->contains('id', $health->id)
        );
    }

    /** The picker leads with what this pocket actually uses. */
    public function test_the_categories_are_ordered_by_how_often_they_are_used(): void
    {
        $user = User::factory()->create();
        $this->finance->syncDefaults($user);

        $pets = FinanceCategory::query()->where('user_id', $user->id)->where('key', 'pets')->firstOrFail();

        Transaction::query()->create([
            'user_id' => $user->id,
            'category_id' => $pets->id,
            'kind' => 'expense',
            'amount' => 50000,
            'date' => now()->toDateString(),
        ]);

        $this->assertSame(
            $pets->id,
            $this->finance->categoriesByUse($user, TransactionKind::Expense)->first()->id
        );
    }

    /** @return array<int, array{key: string, kind: string, keywords: string}> */
    private function defaults(): array
    {
        return (new ReflectionClass(FinanceService::class))->getConstant('DEFAULT_CATEGORIES');
    }

    /** @return list<string> */
    private function defaultKeys(): array
    {
        return array_column($this->defaults(), 'key');
    }
}
