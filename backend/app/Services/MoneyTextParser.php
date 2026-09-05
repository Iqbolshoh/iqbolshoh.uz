<?php

namespace App\Services;

use App\Enums\TransactionKind;
use App\Models\FinanceCategory;
use Illuminate\Support\Collection;

/**
 * Turns "ovqat 25 ming" into an amount, a direction and a category.
 *
 * This is the whole point of logging money from a phone: if it takes four taps
 * and a menu, the coffee never gets written down. So the bot accepts what a
 * person would actually type, in any of the four languages it speaks, in
 * either order — "taksi 12k" and "12000 такси" mean the same thing.
 *
 * What it deliberately does NOT do is guess. When no category matches, the
 * amount still comes back and the bot asks which bucket it belongs to, rather
 * than filing it somewhere plausible and wrong.
 */
class MoneyTextParser
{
    /**
     * Multipliers, longest first so "ming" is tried before "m" could ever be.
     * Tajik and Uzbek share most of these; Russian brings its own.
     */
    private const MULTIPLIERS = [
        'million' => 1000000,
        'millon' => 1000000,
        'миллион' => 1000000,
        'млн' => 1000000,
        'mln' => 1000000,
        'ming' => 1000,
        'minг' => 1000,
        'минг' => 1000,
        'ҳазор' => 1000,
        'хазор' => 1000,
        'тысяч' => 1000,
        'тыс' => 1000,
        'k' => 1000,
        'к' => 1000,
        'm' => 1000000,
    ];

    /** Words that flip the direction to income, in all four languages. */
    private const INCOME_WORDS = [
        // Deliberately NOT here: "oldim" and "гирифтам". They mean both "I
        // received" and "I bought", and filing an expense as income does not
        // just lose a row — it moves the balance twice as far the wrong way.
        'kirim', 'daromad', 'oylik', 'maosh', 'ish haqi', 'tushdi',
        'income', 'salary', 'earned', 'received',
        'доход', 'зарплата', 'приход', 'получил', 'поступление',
        'даромад', 'маош', 'воридот',
    ];

    /**
     * @param  Collection<int, FinanceCategory>  $categories  the owner's own categories, both kinds
     * @return array{amount: int, kind: TransactionKind, category: ?FinanceCategory, note: ?string}|null
     */
    public function parse(string $text, Collection $categories): ?array
    {
        $normalised = $this->normalise($text);

        $amount = $this->extractAmount($normalised, $remainder);

        if ($amount === null || $amount <= 0) {
            return null;
        }

        // Direction first if the text states it outright ("+", "oylik"),
        // otherwise let the category decide: a hit on Salary means money came
        // in, whatever words were used to say so. That way a category the
        // owner adds themselves teaches the parser its own direction, instead
        // of every new income word having to be added to a list in here.
        $explicit = $this->explicitKind($text, $remainder);

        $match = $this->matchCategory(
            $remainder,
            $explicit === null ? $categories : $categories->where('kind', $explicit)
        );

        $kind = $explicit
            ?? $match['category']?->kind
            ?? TransactionKind::Expense;

        return [
            'amount' => $amount,
            'kind' => $kind,
            'category' => $match['category'] ?? null,
            'note' => $this->note($remainder, $match['word'] ?? null),
        ];
    }

    /**
     * Lower case, and every kind of space a phone keyboard can produce reduced
     * to a plain one. A non-breaking space pasted from a bank SMS otherwise
     * splits "25 000" into two numbers.
     */
    private function normalise(string $text): string
    {
        $text = mb_strtolower(trim($text));
        $text = preg_replace('/[\x{00A0}\x{202F}\x{2009}\x{2007}]/u', ' ', $text) ?? $text;
        $text = str_replace(['’', '‘', '`'], "'", $text);
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        // The sign has already been read off the raw text by explicitKind, and
        // leaving it here costs the category: a keyword only counts at the
        // start of a word, so "+oylik 5 mln" — the example the help screen
        // gives — matched nothing at all and landed as uncategorised income.
        return ltrim($text, '+- ');
    }

    /**
     * The smallest number this bot will believe is money, when nothing scales
     * it.
     *
     * Below this the number is almost always part of a sentence rather than a
     * price: "ertalab 8 da yugurish" is a plan, and reading it as 8 so'm did
     * not just answer the wrong question — it wrote a row into the ledger and
     * moved the day's total. Nothing in this country costs two digits, so the
     * floor costs nothing real and stops the whole class of mistake. A number
     * that carries a multiplier ("8k", "8 ming") is exempt: it says outright
     * that it is an amount.
     */
    private const MIN_BARE_AMOUNT = 100;

    /**
     * The first amount in the string, with its multiplier applied.
     *
     * Every number is tried in turn rather than only the first, so a sentence
     * that opens with a time still gives up its price: "8 da taksi 12000" is
     * twelve thousand som, not eight. Whatever is left of the sentence comes
     * back in `$remainder` for the category match.
     */
    private function extractAmount(string $text, ?string &$remainder): ?int
    {
        // Grouped thousands first ("25 000", "1'200"), then a plain or decimal
        // number. Trying it the other way round would match just the "25".
        $pattern = "/(\d{1,3}(?:[ ',.]\d{3})+|\d+(?:[.,]\d+)?)\s*([a-zа-яёғқҳў]{1,7})?/u";

        if (! preg_match_all($pattern, $text, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
            $remainder = $text;

            return null;
        }

        foreach ($matches as $match) {
            [$rawNumber, $numberOffset] = $match[1];
            $suffix = $match[2][0] ?? '';

            $multiplier = self::MULTIPLIERS[$suffix] ?? null;

            $amount = $this->toNumber($rawNumber);

            if ($amount === null || $this->isClockTime($text, $rawNumber, $numberOffset)) {
                continue;
            }

            // The suffix is only eaten when it really was a multiplier: in
            // "25000 taksi" the word is the category, not a scale.
            $consumed = $numberOffset + strlen($rawNumber);

            if ($multiplier !== null) {
                $amount *= $multiplier;
                $consumed = $match[2][1] + strlen($suffix);
            } elseif ($amount < self::MIN_BARE_AMOUNT) {
                continue;
            }

            $remainder = trim(substr($text, 0, $numberOffset) . ' ' . substr($text, $consumed));

            return (int) round($amount);
        }

        $remainder = $text;

        return null;
    }

    /**
     * Whether this number is one half of a clock reading.
     *
     * "8:30" survives the floor above as 830 once the colon is stripped, so it
     * has to be recognised where it still has its punctuation: a number with a
     * colon on either side of it is a time, and a time is never a price.
     */
    private function isClockTime(string $text, string $rawNumber, int $offset): bool
    {
        $before = $offset > 0 ? substr($text, $offset - 1, 1) : '';
        $after = substr($text, $offset + strlen($rawNumber), 1);

        return $before === ':' || $after === ':';
    }

    /** "25 000" and "1,5" both become a number; the separators mean different things. */
    private function toNumber(string $raw): ?float
    {
        // A single comma or dot with one or two digits after it is a decimal
        // ("1.5 mln"); anything else in a grouped number is a separator.
        if (preg_match('/^\d+[.,]\d{1,2}$/', $raw)) {
            return (float) str_replace(',', '.', $raw);
        }

        $digits = preg_replace('/\D/', '', $raw);

        return $digits === '' ? null : (float) $digits;
    }

    /**
     * The direction the text states outright, or null when it says nothing and
     * the category has to settle it.
     *
     * A leading "+" is checked against the raw text because normalising keeps
     * it but the amount extraction does not.
     */
    private function explicitKind(string $rawText, string $remainder): ?TransactionKind
    {
        if (str_starts_with(trim($rawText), '+')) {
            return TransactionKind::Income;
        }

        foreach (self::INCOME_WORDS as $word) {
            if (str_contains($remainder, $word)) {
                return TransactionKind::Income;
            }
        }

        return null;
    }

    /**
     * The best category for what is left of the sentence.
     *
     * Longer keywords win: "ish haqi" must beat a category whose keyword list
     * happens to contain "ish". A keyword only counts on a word boundary, so
     * "kino" never matches inside "kinoya".
     *
     * @param  Collection<int, FinanceCategory>  $categories
     * @return array{category: FinanceCategory, word: string}|null
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
                if ($word === '' || mb_strlen($word) < 2 || mb_strlen($word) <= $bestLength) {
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

    /**
     * What the owner wrote beyond the amount and the category name, kept as
     * the note. An empty note is null rather than "", so the panel can tell
     * "nothing written" from "written and then cleared".
     */
    private function note(string $remainder, ?string $matchedWord): ?string
    {
        $note = $remainder;

        if ($matchedWord !== null && $matchedWord !== '') {
            // The whole whitespace-delimited token, not just the keyword:
            // "dorixona" recognised through "dori" would otherwise leave the
            // note reading "xona", and "kafede" would leave "de".
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
