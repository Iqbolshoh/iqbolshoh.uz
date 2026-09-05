<?php

namespace Tests\Feature\Finance;

use App\Models\TelegramAccount;
use App\Models\User;
use App\Services\FinanceService;
use App\Services\TelegramBot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Lang;
use Tests\TestCase;

/**
 * The bot's four languages.
 *
 * Two failures these guard against, both of which look fine from the outside:
 * a locale file that is missing lines (the reader silently gets English), and
 * a locale file that was copied from its neighbour and never translated (every
 * key-counting test passes, and the bot answers Tajik in Russian).
 */
class BotLanguageTest extends TestCase
{
    use RefreshDatabase;

    private const LOCALES = ['uz', 'ru', 'en', 'tj'];

    /**
     * Lines two languages are allowed to agree on.
     *
     * "so'm" is the currency's own name, not a word to translate — English
     * prose about Uzbek money writes it exactly the same way, and forcing a
     * difference here would mean inventing a wrong one.
     */
    private const SHARED_BY_DESIGN = ['currency'];

    public function test_every_locale_carries_every_line(): void
    {
        foreach (['bot', 'finance'] as $file) {
            $reference = $this->flatten(Lang::get($file, [], 'en'));

            foreach (self::LOCALES as $locale) {
                $lines = $this->flatten(Lang::get($file, [], $locale));

                $this->assertSame(
                    [],
                    array_diff(array_keys($reference), array_keys($lines)),
                    "lang/{$locale}/{$file}.php is missing lines"
                );

                $this->assertSame(
                    [],
                    array_diff(array_keys($lines), array_keys($reference)),
                    "lang/{$locale}/{$file}.php has lines English does not"
                );
            }
        }
    }

    /**
     * No two languages may say the same thing.
     *
     * The exception is a line made only of placeholders and punctuation — those
     * genuinely read the same everywhere, and demanding a difference would
     * force a fake one.
     */
    public function test_no_locale_is_a_copy_of_another(): void
    {
        foreach (['bot', 'finance'] as $file) {
            foreach (self::LOCALES as $locale) {
                foreach (self::LOCALES as $other) {
                    if ($locale >= $other) {
                        continue;
                    }

                    $mine = $this->flatten(Lang::get($file, [], $locale));
                    $theirs = $this->flatten(Lang::get($file, [], $other));

                    $shared = array_filter(
                        array_keys($mine),
                        fn (string $key): bool => ($mine[$key] ?? null) === ($theirs[$key] ?? null)
                            && ! in_array($key, self::SHARED_BY_DESIGN, true)
                            && $this->carriesWords($mine[$key])
                    );

                    $this->assertSame(
                        [],
                        array_values($shared),
                        "lang/{$locale}/{$file}.php and lang/{$other}/{$file}.php share wording"
                    );
                }
            }
        }
    }

    /**
     * No block may define the same key twice.
     *
     * PHP keeps the last one and says nothing, so the earlier line is simply
     * gone: the button that read it goes blank, or worse, quietly starts
     * showing the other value. Nothing downstream can see it — the array that
     * reaches Lang has already lost the duplicate, so key counts and the
     * coverage tests above all pass. Only the source can be asked.
     */
    public function test_no_language_file_defines_a_key_twice(): void
    {
        foreach (self::LOCALES as $locale) {
            foreach (['bot', 'finance'] as $file) {
                $path = lang_path("{$locale}/{$file}.php");

                $this->assertSame(
                    [],
                    $this->duplicateKeysIn($path),
                    "lang/{$locale}/{$file}.php defines a key twice in one block"
                );
            }
        }
    }

    /**
     * Walk the file's tokens and collect every key defined twice inside the
     * same pair of brackets.
     *
     * A stack rather than a depth counter, because two sibling blocks are at
     * the same depth and are allowed to share key names — 'title' belongs to
     * several of them.
     *
     * @return list<string>
     */
    private function duplicateKeysIn(string $path): array
    {
        $tokens = array_values(array_filter(
            token_get_all(file_get_contents($path)),
            fn ($token): bool => ! is_array($token) || ! in_array($token[0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)
        ));

        $stack = [];
        $duplicates = [];

        foreach ($tokens as $index => $token) {
            if ($token === '[') {
                $stack[] = [];

                continue;
            }

            if ($token === ']') {
                array_pop($stack);

                continue;
            }

            $next = $tokens[$index + 1] ?? null;

            if (! is_array($token)
                || $token[0] !== T_CONSTANT_ENCAPSED_STRING
                || ! is_array($next)
                || $next[0] !== T_DOUBLE_ARROW
                || $stack === []) {
                continue;
            }

            $key = trim($token[1], "'\"");
            $top = array_key_last($stack);

            if (isset($stack[$top][$key])) {
                $duplicates[] = $key;
            }

            $stack[$top][$key] = true;
        }

        return $duplicates;
    }

    public function test_the_language_button_switches_the_chat(): void
    {
        config(['services.telegram.token' => 'test-token']);
        Http::fake(['api.telegram.org/*' => Http::response(['ok' => true, 'result' => ['message_id' => 1]])]);

        $user = User::factory()->create(['timezone' => 'Asia/Samarkand']);
        app(FinanceService::class)->ensureDefaults($user);

        $account = TelegramAccount::query()->create([
            'user_id' => $user->id,
            'telegram_id' => 5339820458,
            'is_active' => true,
        ]);

        app(TelegramBot::class)->handleCallback([
            'message' => ['chat' => ['id' => 5339820458], 'message_id' => 7],
            'data' => 'lang:ru',
        ]);

        $this->assertSame('ru', $account->fresh()->locale);

        Http::assertSent(fn ($request) => str_contains((string) $request['text'], 'русский')
            || str_contains((string) $request['text'], 'Деньги'));
    }

    /** Telegram calls Tajik "tg"; this bot's files call it "tj". */
    public function test_telegrams_tajik_code_maps_to_this_bots_files(): void
    {
        config(['services.telegram.token' => 'test-token']);
        Http::fake(['api.telegram.org/*' => Http::response(['ok' => true, 'result' => ['message_id' => 1]])]);

        app(TelegramBot::class)->handleMessage([
            'chat' => ['id' => 999999],
            'from' => ['language_code' => 'tg'],
            'text' => '/start',
        ]);

        Http::assertSent(fn ($request) => str_contains((string) $request['text'], 'Ин бот шахсӣ аст'));
    }

    /** @return array<string, string> */
    private function flatten(array $lines, string $prefix = ''): array
    {
        $flat = [];

        foreach ($lines as $key => $value) {
            $path = $prefix === '' ? (string) $key : "{$prefix}.{$key}";

            if (is_array($value)) {
                $flat += $this->flatten($value, $path);

                continue;
            }

            $flat[$path] = $value;
        }

        return $flat;
    }

    /** Whether a line contains anything a translator could have changed. */
    private function carriesWords(string $line): bool
    {
        $stripped = preg_replace('/:[a-z_]+|<[^>]+>|[^\p{L}]/u', '', $line);

        return mb_strlen((string) $stripped) > 0;
    }
}
