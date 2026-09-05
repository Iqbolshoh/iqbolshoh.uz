<?php

namespace Tests\Feature\Activity;

use App\Models\User;
use App\Services\ActivityService;
use App\Services\DurationTextParser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * What the bot understands when the day is described to it in one line.
 *
 * The table is the specification: every row is something worth being able to
 * type with one hand at the end of a day.
 */
class DurationTextParserTest extends TestCase
{
    use RefreshDatabase;

    private DurationTextParser $parser;

    private $categories;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        app(ActivityService::class)->ensureDefaults($user);

        $this->parser = new DurationTextParser();
        $this->categories = app(ActivityService::class)->categories($user);
    }

    #[DataProvider('phrases')]
    public function test_it_reads_a_phrase(string $text, ?int $minutes, ?string $categoryKey): void
    {
        $result = $this->parser->parse($text, $this->categories);

        if ($minutes === null) {
            $this->assertNull($result, "“{$text}” should not be read as a duration");

            return;
        }

        $this->assertNotNull($result, "“{$text}” should have been understood");
        $this->assertSame($minutes, $result['minutes'], "wrong length for “{$text}”");
        $this->assertSame($categoryKey, $result['category']?->key, "wrong activity for “{$text}”");
    }

    public static function phrases(): array
    {
        return [
            'the line the owner asked for' => ['8 soat uxladim', 480, 'sleep'],
            'work in hours' => ['3 soat ish', 180, 'work'],
            'a lesson' => ['2 soat dars', 120, 'study'],
            'minutes' => ['45 daqiqa yugurdim', 45, 'sport'],

            // Written the way people write it, and the halves must not be lost.
            'hours and minutes together' => ['1 soat 30 daq telefonda', 90, 'scrolling'],
            'short unit' => ['20 daq namoz', 20, 'prayer'],
            'the activity first' => ['yo\'lda 40 daqiqa', 40, 'travel'],

            'russian' => ['спал 7 часов', 420, 'sleep'],
            'english' => ['work 6 hours', 360, 'work'],
            'tajik' => ['3 соат кор', 180, 'work'],

            'a decimal hour' => ['1.5 soat sport', 90, 'sport'],
            'no activity named' => ['2 soat', 120, null],

            // A duration says its unit out loud. Without one there is nothing
            // to tell eight hours from eight minutes from eight o'clock, and
            // filing it as any of the three would be a guess.
            'a bare number is not a duration' => ['8 uxladim', null, null],
            'a clock reading is not a duration' => ['9:30 da uchrashuv', null, null],
            'money is not a duration' => ['ovqat 25000', null, null],
            'nothing at all' => ['salom', null, null],

            // The money parser reads a bare "m" as a million and durations are
            // tried first, so this line must fall straight through to it.
            'a lone m belongs to money' => ['5 m kompyuter', null, null],

            // Nobody spends more than a day on one thing in one entry, and
            // accepting it would poison every average the report computes.
            'more than a day is capped' => ['100 soat ish', 1440, 'work'],
        ];
    }

    /**
     * The guided flow has already named the activity and asked how long, so a
     * bare number is an answer rather than a guess.
     */
    public function test_a_bare_number_answers_the_question_it_was_asked(): void
    {
        // Under twelve reads as hours, above it as minutes — the boundary
        // people already use when they say "I slept 8" and "I ran 40".
        $this->assertSame(480, $this->parser->durationOnly('8'));
        $this->assertSame(40, $this->parser->durationOnly('40'));
        $this->assertSame(90, $this->parser->durationOnly('1.5'));

        // A unit still means exactly what it says.
        $this->assertSame(45, $this->parser->durationOnly('45 daqiqa'));
        $this->assertSame(120, $this->parser->durationOnly('2 soat'));

        $this->assertNull($this->parser->durationOnly('salom'));
    }

    /** The leftover words become the note, without the stem of the matched word. */
    public function test_the_note_keeps_only_what_was_not_understood(): void
    {
        $result = $this->parser->parse('2 soat ish api ustida', $this->categories);

        $this->assertSame('work', $result['category']->key);
        $this->assertSame('api ustida', $result['note']);
    }
}
