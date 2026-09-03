<?php

namespace Tests\Feature\Telegram;

use App\Console\Commands\TelegramProfile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Lang;
use ReflectionClass;
use Tests\TestCase;

/**
 * The bot's public face, which lives on Telegram's side and not in this repo.
 *
 * Nothing here talks to Telegram; the point is the three ways this quietly goes
 * wrong, none of which show up in the chat itself:
 *
 *  - the default scope is skipped, so everyone whose client speaks a fifth
 *    language keeps whatever list was there before;
 *  - Tajik is pushed as "tj", the code this app uses, rather than "tg", the
 *    one Telegram files it under, and lands in nobody's menu;
 *  - the menu offers a command the bot does not answer, which reads as a
 *    broken bot rather than a missing line.
 */
class BotProfileTest extends TestCase
{
    private const LOCALES = ['uz', 'ru', 'en', 'tj'];

    /** Telegram's own caps. Over any of them the call is rejected outright. */
    private const LIMITS = ['short' => 120, 'description' => 512];

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.telegram.token' => 'test-token']);

        Http::fake([
            'api.telegram.org/*' => Http::response(['ok' => true, 'result' => true]),
        ]);
    }

    public function test_every_language_gets_the_menu_including_the_default_scope(): void
    {
        $this->artisan('telegram:profile', ['--commands' => true])->assertSuccessful();

        $codes = $this->sentLanguageCodes('setMyCommands');

        // '' is the default scope: absent, a client on an unlisted language
        // sees no menu at all.
        $this->assertSame(['', 'uz', 'ru', 'en', 'tg'], $codes);
        $this->assertNotContains('tj', $codes, 'Telegram knows Tajik as "tg", not "tj"');
    }

    public function test_the_descriptions_go_out_in_every_language(): void
    {
        $this->artisan('telegram:profile')->assertSuccessful();

        foreach (['setMyCommands', 'setMyDescription', 'setMyShortDescription'] as $method) {
            $this->assertSame(
                ['', 'uz', 'ru', 'en', 'tg'],
                $this->sentLanguageCodes($method),
                "{$method} did not reach every language"
            );
        }
    }

    /**
     * The name and the avatar are one value each, not one per language, so
     * they are sent once. Repeating them per scope would spend the rate limit
     * setMyName is given for nothing.
     */
    public function test_the_name_and_avatar_are_sent_once(): void
    {
        $this->artisan('telegram:profile')->assertSuccessful();

        $this->assertSame([''], $this->sentLanguageCodes('setMyName'));
        $this->assertCount(1, $this->sentTo('setMyProfilePhoto'));
    }

    public function test_commands_only_leaves_the_name_and_descriptions_alone(): void
    {
        $this->artisan('telegram:profile', ['--commands' => true])->assertSuccessful();

        $this->assertSame([], $this->sentLanguageCodes('setMyName'));
        $this->assertSame([], $this->sentLanguageCodes('setMyDescription'));
        $this->assertSame([], $this->sentTo('setMyProfilePhoto'));
    }

    public function test_a_dry_run_sends_nothing(): void
    {
        $this->artisan('telegram:profile', ['--dry-run' => true])->assertSuccessful();

        Http::assertNothingSent();
    }

    /**
     * A command in the menu that the bot does not answer.
     *
     * The menu is Telegram's, so nothing in this codebase fails when the two
     * drift apart — the person just taps a command and is shown the welcome
     * screen as though they had typed nonsense.
     */
    public function test_every_offered_command_is_one_the_bot_answers(): void
    {
        $source = file_get_contents(app_path('Services/TelegramBot.php'));

        foreach ($this->offeredCommands() as $command) {
            $this->assertStringContainsString(
                "'/{$command}'",
                $source,
                "/{$command} is offered in the menu but TelegramBot handles no such command"
            );
        }
    }

    public function test_every_language_describes_every_command(): void
    {
        foreach ($this->offeredCommands() as $command) {
            foreach (self::LOCALES as $locale) {
                // The third argument matters: without it Lang::has falls back
                // to English and reports a missing line as present.
                $this->assertTrue(
                    Lang::has("bot.cmd.{$command}", $locale, false),
                    "bot.cmd.{$command} is missing from lang/{$locale}/bot.php"
                );
            }
        }
    }

    public function test_no_profile_line_is_longer_than_telegram_allows(): void
    {
        foreach (self::LOCALES as $locale) {
            foreach (self::LIMITS as $field => $limit) {
                $text = (string) __("bot.profile.{$field}", [], $locale);

                $this->assertLessThanOrEqual(
                    $limit,
                    mb_strlen($text),
                    "bot.profile.{$field} in {$locale} is over Telegram's {$limit} characters"
                );
            }
        }

        $this->assertLessThanOrEqual(64, mb_strlen((string) config('services.telegram.bot_name')));
    }

    /**
     * Telegram crops an avatar to a circle, so anything but a square is cut
     * unevenly — and a missing file turns the whole command red.
     */
    public function test_the_avatar_ships_with_the_code_and_is_square(): void
    {
        $path = resource_path('telegram/avatar.png');

        $this->assertFileExists($path);

        [$width, $height] = getimagesize($path);

        $this->assertSame($width, $height, 'the avatar has to be square');
        $this->assertGreaterThanOrEqual(512, $width, 'Telegram wants at least 512 pixels');
    }

    /**
     * The command menu is a constant on the command class rather than a list
     * repeated here: a test that keeps its own copy proves only that the copy
     * agrees with itself.
     *
     * @return list<string>
     */
    private function offeredCommands(): array
    {
        return (new ReflectionClass(TelegramProfile::class))->getConstant('COMMANDS');
    }

    /**
     * The language codes one Bot API method was called with, in order.
     *
     * @return list<string>
     */
    private function sentLanguageCodes(string $method): array
    {
        $codes = [];

        foreach (Http::recorded() as [$request]) {
            if (! str_ends_with($request->url(), '/' . $method)) {
                continue;
            }

            // The default scope omits the field entirely rather than sending
            // it empty, so a missing key is that scope, not a lost value.
            $codes[] = (string) ($request->data()['language_code'] ?? '');
        }

        return $codes;
    }

    /**
     * Every call made to one Bot API method, for the ones that carry no
     * language of their own.
     *
     * @return list<\Illuminate\Http\Client\Request>
     */
    private function sentTo(string $method): array
    {
        $sent = [];

        foreach (Http::recorded() as [$request]) {
            if (str_ends_with($request->url(), '/' . $method)) {
                $sent[] = $request;
            }
        }

        return $sent;
    }
}
