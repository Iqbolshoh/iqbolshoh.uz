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
