<?php

namespace Tests\Feature\Finance;

use App\Models\User;
use App\Services\FinanceService;
use App\Services\MoneyTextParser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * What the bot understands when it is written to like a person.
 *
 * The table is the specification: every line here is something worth being
 * able to type from a phone with one hand.
 */
class MoneyTextParserTest extends TestCase
{
    use RefreshDatabase;

    private MoneyTextParser $parser;

    private $categories;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        app(FinanceService::class)->ensureDefaults($user);

        $this->parser = new MoneyTextParser();
        $this->categories = app(FinanceService::class)->categories($user);
    }

    #[DataProvider('phrases')]
    public function test_it_reads_a_phrase(string $text, ?int $amount, ?string $kind, ?string $categoryKey): void
    {
        $result = $this->parser->parse($text, $this->categories);

        if ($amount === null) {
            $this->assertNull($result, "“{$text}” should not be read as money");

            return;
        }

        $this->assertNotNull($result, "“{$text}” should have been understood");
        $this->assertSame($amount, $result['amount'], "wrong amount for “{$text}”");
        $this->assertSame($kind, $result['kind']->value, "wrong direction for “{$text}”");
        $this->assertSame($categoryKey, $result['category']?->key, "wrong category for “{$text}”");
    }

    public static function phrases(): array
    {
        return [
            'amount after the word' => ['ovqat 25000', 25000, 'expense', 'food'],
            'amount before the word' => ['25000 ovqat', 25000, 'expense', 'food'],
            'k suffix' => ['taksi 12k', 12000, 'expense', 'taxi'],
            'grouped thousands' => ['dorixona 87 500', 87500, 'expense', 'pharmacy'],
            'ming suffix' => ['kitob 120 ming', 120000, 'expense', 'education'],
            'decimal with a multiplier' => ['1.5 mln kompyuter', 1500000, 'expense', 'tech'],
            'russian' => ['такси 12000', 12000, 'expense', 'taxi'],
            'tajik' => ['хӯрок 40000', 40000, 'expense', 'food'],
            'english' => ['food 15000', 15000, 'expense', 'food'],
            'leading plus is income' => ['+200000', 200000, 'income', null],

            // A keyword only counts at the start of a word, so the sign has to
            // come off before the match — "+oylik" is the example the help
            // screen gives, and it used to land as uncategorised income.
            'leading plus keeps its category' => ['+oylik 5 mln', 5000000, 'income', 'salary'],
            'income by word' => ['oylik tushdi 5 mln', 5000000, 'income', 'salary'],

            // The category decides the direction when the words do not: this is
            // what keeps every new income word from having to be listed twice.
            'income by category alone' => ['zarplata 4500000', 4500000, 'income', 'salary'],

            // "oldim" means both "I received" and "I bought". Reading it as
            // income would move the balance twice the wrong way.
            'ambiguous verb stays an expense' => ['kitobni oldim 45000', 45000, 'expense', 'education'],

            'uzbek suffix on the keyword' => ['taksida 15000', 15000, 'expense', 'taxi'],
            'amount with no category' => ['35000', 35000, 'expense', null],
            'no amount at all' => ['salom qalaysiz', null, null, null],

            // The buckets the reorganisation split apart have to stay apart:
            // each of these pairs used to land in one category and told the
            // owner nothing a month later.
            'shopping is not eating out' => ['bozor 180000', 180000, 'expense', 'food'],
            'eating out is not shopping' => ['restoran 95000', 95000, 'expense', 'cafe'],
            'a bus is not a taxi' => ['avtobus 2000', 2000, 'expense', 'transport'],
            'rent is not the utility bill' => ['ijara 2 mln', 2000000, 'expense', 'rent'],
            'the utility bill is not rent' => ['svet 140000', 140000, 'expense', 'utilities'],
            'the pharmacy is not the clinic' => ['apteka 45000', 45000, 'expense', 'pharmacy'],
            'the clinic is not the pharmacy' => ['stomatolog 600000', 600000, 'expense', 'doctor'],

            // The longer phrase wins, which is the only thing keeping the
            // narrow category reachable at all once a broad one shares a word.
            'a longer keyword beats a shorter one' => ['parkovka 5000', 5000, 'expense', 'fuel'],
            'the shorter keyword still works alone' => ['gaz 90000', 90000, 'expense', 'utilities'],

            // A plan is not a payment. Reading the "8" here as money did not
            // just answer wrongly — it wrote a row and moved the day's total.
            'a time of day is not an amount' => ['ertalab 8 da yugurish', null, null, null],
            'a clock reading is not an amount' => ['uchrashuv 9:30 da', null, null, null],
            'two digits alone are not money' => ['kofe 15', null, null, null],
            'two digits with a multiplier are money' => ['kofe 15k', 15000, 'expense', 'cafe'],

            // ...but a real price later in the same sentence still counts.
            'a price after a time is still a price' => ['8 da taksi 12000', 12000, 'expense', 'taxi'],
        ];
    }

    /** The leftover words become the note, without the stem of the matched word. */
    public function test_the_note_keeps_only_what_was_not_understood(): void
    {
        $result = $this->parser->parse('podpiska 1 200 000 server uchun', $this->categories);

        $this->assertSame('subscriptions', $result['category']->key);
        $this->assertSame('server uchun', $result['note']);
    }

    public function test_a_matched_word_never_leaves_a_stem_in_the_note(): void
    {
        // "dorixona" is recognised through the keyword "dori"; a naive strip
        // would leave the note reading "xona".
        $result = $this->parser->parse('dorixona 87 500', $this->categories);

        $this->assertSame('pharmacy', $result['category']->key);
        $this->assertNull($result['note']);
    }
}
