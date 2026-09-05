<?php

namespace App\Services;

use App\Models\ActivityCategory;
use Illuminate\Support\Collection;

/**
 * Turns "8 soat uxladim" into eight hours of sleep.
 *
 * Built to the same rule as MoneyTextParser: one line of text is one recorded
 * thing, because if it takes four taps and a menu the day never gets written
 * down. The difference is that a duration says its own unit out loud — "soat",
 * "daqiqa", "часа", "min" — and that unit is what makes it a duration at all.
 *
 * So unlike money, a bare number is never a duration here. "8" on its own
 * could be eight hours, eight minutes or a plan for eight o'clock, and the one
 * thing worse than asking is filing it as one of the three.
 */
class DurationTextParser
{
    /**
     * Unit words, longest first within each group so "daqiqa" is tried before
     * anything could match "daq" and "soatlik" before "soat".
     *
     * @var array<string, int>  word => minutes it is worth
     */
    private const UNITS = [
        // Hours
        'soatlik' => 60,
        'soat' => 60,
        'soa' => 60,
        'часов' => 60,
        'часа' => 60,
        'час' => 60,
        'ч' => 60,
        'соат' => 60,
        'hours' => 60,
        'hour' => 60,
        'hrs' => 60,
        'hr' => 60,
        'h' => 60,

        // Minutes
        'daqiqa' => 1,
        'daqiq' => 1,
        'daq' => 1,
        'minut' => 1,
        'минут' => 1,
        'мин' => 1,
        'дақиқа' => 1,
        'minutes' => 1,
        'minute' => 1,
        'mins' => 1,
        'min' => 1,

        // Deliberately NOT here: a bare "m". The money parser reads it as a
        // million, and durations are tried first — so "5 m kompyuter" would
        // have become five minutes of nothing instead of five million som.
        // Nobody writes minutes as "m" in these languages anyway; they write
        // "daq" or "мин", and both are above.
    ];

    /**
     * Nobody spends more than a day on one thing in one entry.
     *
     * A number past this is a typo or a misread unit, and silently accepting
     * "800 soat" would poison every average the report computes.
     */
    private const MAX_MINUTES = 1440;

    /**
     * @param  Collection<int, ActivityCategory>  $categories
     * @return array{minutes: int, category: ?ActivityCategory, note: ?string}|null
     */
    public function parse(string $text, Collection $categories): ?array
    {
        $normalised = $this->normalise($text);

        $minutes = $this->extractMinutes($normalised, $remainder);

        if ($minutes === null || $minutes <= 0) {
            return null;
        }

        $match = $this->matchCategory($remainder, $categories);

        return [
            'minutes' => min($minutes, self::MAX_MINUTES),
            'category' => $match['category'] ?? null,
            'note' => $this->note($remainder, $match['word'] ?? null),
        ];
    }

    /**
     * A duration on its own, for the guided flow.
     *
     * The activity has already been tapped and the bot has asked how long, so
     * a bare "90" is ninety minutes and "2" is two hours — the shape people
     * actually type when answering that question. A number with a unit still
     * means exactly what it says.
     */
    public function durationOnly(string $text): ?int
    {
        $normalised = $this->normalise($text);
        $remainder = null;

        $minutes = $this->extractMinutes($normalised, $remainder);

        if ($minutes !== null) {
            return min($minutes, self::MAX_MINUTES);
        }

        if (! preg_match('/^\d+(?:[.,]\d+)?$/', trim($normalised), $match)) {
            return null;
        }

        $number = (float) str_replace(',', '.', $match[0]);

        // Under twelve it reads as hours, above it as minutes. That is the
        // boundary people already use when they say "I slept 8" and "I ran 40"
        // — and it is why the question names the unit in its own examples.
        $minutes = $number < 12 ? (int) round($number * 60) : (int) round($number);

        return $minutes > 0 ? min($minutes, self::MAX_MINUTES) : null;
    }

    /** Lower case, and every kind of space a phone keyboard can produce reduced to one. */
    private function normalise(string $text): string
    {
        $text = mb_strtolower(trim($text));
        $text = preg_replace('/[\x{00A0}\x{202F}\x{2009}\x{2007}]/u', ' ', $text) ?? $text;
        $text = str_replace(['’', '‘', '`'], "'", $text);

        return preg_replace('/\s+/u', ' ', $text) ?? $text;
    }

    /**
     * Every "<number> <unit>" pair in the line, added together.
     *
     * Added rather than "first wins", because "2 soat 30 daqiqa" is one
     * duration written the way people write it, and taking only the two would
     * lose half an hour on every entry shaped like that. A clock reading
     * ("9:30") is skipped: it is a time of day, not a length of one.
     */
    private function extractMinutes(string $text, ?string &$remainder): ?int
    {
        $units = implode('|', array_map(
            fn (string $unit): string => preg_quote($unit, '/'),
            array_keys(self::UNITS)
        ));

        $pattern = '/(?<![\d:])(\d+(?:[.,]\d+)?)\s*(' . $units . ')(?![\p{L}\d])/u';

        if (! preg_match_all($pattern, $text, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
            $remainder = $text;

            return null;
        }

        $minutes = 0.0;
        $remainder = $text;

        foreach ($matches as $match) {
            $minutes += (float) str_replace(',', '.', $match[1][0]) * self::UNITS[$match[2][0]];

            $remainder = str_replace($match[0][0], ' ', $remainder);
        }

        $remainder = trim(preg_replace('/\s+/u', ' ', $remainder) ?? $remainder);

        return (int) round($minutes);
    }

    /**
     * The best activity for what is left of the sentence.
     *
     * Longer keywords win, exactly as in the money parser: "kitob oqidim" must
     * beat an activity whose list happens to contain "kitob". A keyword only
     * counts at the start of a word, so "dars" never matches inside "gardash".
     *
     * @param  Collection<int, ActivityCategory>  $categories
     * @return array{category: ActivityCategory, word: string}|null
     */
    private function matchCategory(string $remainder, Collection $categories): ?array
    {
        if ($remainder === '') {
            return null;
        }

        $best = null;
        $bestLength = 0;

        foreach ($categories as $category) {
            foreach ($category->matchWords() as $word) {
                if ($word === '' || mb_strlen($word) < 3 || mb_strlen($word) <= $bestLength) {
                    continue;
                }

                if (preg_match('/(?:^|\s)' . preg_quote($word, '/') . '/u', $remainder) === 1) {
                    $best = ['category' => $category, 'word' => $word];
                    $bestLength = mb_strlen($word);
                }
            }
        }

        return $best;
    }

    /** Whatever was written beyond the duration and the activity. */
    private function note(string $remainder, ?string $matchedWord): ?string
    {
        $note = $remainder;

        if ($matchedWord !== null && $matchedWord !== '') {
            // The whole whitespace-delimited token, not just the keyword, so
            // "uxladim" recognised through "uxla" does not leave "dim" behind.
            $note = preg_replace(
                '/(?:^|\s)\S*' . preg_quote($matchedWord, '/') . '\S*(?=\s|$)/u',
                ' ',
                $note,
                1
            ) ?? $note;
        }

        $note = trim(preg_replace('/\s+/u', ' ', $note) ?? $note);
        $note = trim($note, " \t\n\r\0\x0B-–—:,.+");

        return $note === '' ? null : mb_substr($note, 0, 255);
    }
}
