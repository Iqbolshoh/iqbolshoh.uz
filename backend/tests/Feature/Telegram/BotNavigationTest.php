<?php

namespace Tests\Feature\Telegram;

use App\Models\Plan;
use App\Models\TelegramAccount;
use App\Models\User;
use App\Services\FinanceService;
use App\Services\TelegramBot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Getting around the bot.
 *
 * The failure this is built for is the one a chat never reports: a button that
 * leads nowhere. Telegram spins its spinner, the acknowledgement stops it, and
 * the screen simply does not change — no error, no log line, nothing to grep
 * for. So every button the bot draws is pressed here, and pressing it has to
 * produce a message.
 */
class BotNavigationTest extends TestCase
{
    use RefreshDatabase;

    private const CHAT_ID = 5339820458;

    private User $user;

    private TelegramBot $bot;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.telegram.token' => 'test-token']);

        $this->fakeTelegram();

        $this->user = User::factory()->create(['timezone' => 'Asia/Samarkand']);

        TelegramAccount::query()->create([
            'user_id' => $this->user->id,
            'telegram_id' => self::CHAT_ID,
            'is_active' => true,
            'linked_at' => now(),
        ]);

        app(FinanceService::class)->ensureDefaults($this->user);

        Plan::query()->create([
            'user_id' => $this->user->id,
            'title' => 'Write the release notes',
            'date' => now($this->user->timezone)->toDateString(),
            'start_time' => '09:00:00',
            'planned_minutes' => 45,
            'status' => 'pending',
        ]);

        $this->bot = app(TelegramBot::class);
    }

    /**
     * Walk the whole bot and press everything it offers.
     *
     * The buttons are collected from what the bot actually sent rather than
     * listed here, so a screen added later is covered the moment something
     * links to it.
     */
    public function test_no_button_leads_nowhere(): void
    {
        foreach (['/start', '/menu', '/today', '/money', '/stats', '/status', '/help', '/language'] as $command) {
            $this->message($command);
        }

        // A recorded row is the only thing that draws the buttons hanging off
        // one — "change category", "delete this" — so the walk has to have
        // written something before it can reach them.
        $this->message('ovqat 25000');

        $pressed = [];
        $queue = $this->offeredCallbacks();

        // Bounded by shape rather than by value: the day arrows carry a date,
        // so pressing "next" forever is an infinite walk into the future and
        // one date proves as much as a thousand.
        while ($queue !== [] && count($pressed) < 60) {
            $shape = $this->shapeOf(array_shift($queue));

            if (isset($pressed[$shape['key']])) {
                continue;
            }

            $pressed[$shape['key']] = true;
            $data = $shape['data'];

            $before = count(Http::recorded());
            $this->press($data);

            $this->assertGreaterThan(
                $before,
                count(Http::recorded()),
                "pressing \"{$data}\" produced no message at all"
            );

            // Whatever that press drew may itself carry buttons nobody has
            // pressed yet.
            $queue = array_merge($queue, $this->offeredCallbacks());
        }

        $this->assertGreaterThan(15, count($pressed), 'the walk did not reach most of the bot');
    }

    /**
     * One button's identity, with the parts that vary per row folded away.
     *
     * A date and a row id make every press look new; what is being tested is
     * the kind of button, of which there are few.
     *
     * @return array{key: string, data: string}
     */
    private function shapeOf(string $data): array
    {
        return [
            'key' => preg_replace('/\d{4}-\d{2}-\d{2}|:\d+/', ':n', $data),
            'data' => $data,
        ];
    }

    /**
     * The bottom keyboard sends words, not callbacks, so each of its labels
     * has to be readable back into a command. A label that is not answers the
     * home screen instead, which looks like the bot ignoring the button.
     */
    public function test_every_bottom_keyboard_label_is_understood(): void
    {
        foreach (['uz', 'ru', 'en', 'tj'] as $locale) {
            foreach (['today', 'money', 'stats', 'status'] as $key) {
                $this->fakeTelegram();

                $this->message((string) __("bot.btn.{$key}", [], $locale));

                $this->assertNotSame(
                    [],
                    $this->offeredCallbacks(),
                    "the {$locale} label for {$key} produced a screen with no buttons"
                );
            }
        }
    }

    /**
     * The arrows carry a date, so walking forward has to keep working past the
     * day after tomorrow rather than folding back onto it.
     */
    public function test_the_day_arrows_walk_past_tomorrow(): void
    {
        $date = now($this->user->timezone)->addDays(4)->toDateString();

        $this->press("nav:day:{$date}");

        Http::assertSent(fn ($request) => str_contains(
            (string) ($request->data()['text'] ?? ''),
            now($this->user->timezone)->addDays(4)->translatedFormat('j F')
        ));
    }

    /** A mangled date must land on today, not on some arbitrary day. */
    public function test_a_broken_date_falls_back_to_today(): void
    {
        $this->press('nav:day:not-a-date');

        Http::assertSent(fn ($request) => str_contains(
            (string) ($request->data()['text'] ?? ''),
            now($this->user->timezone)->translatedFormat('j F')
        ));
    }

    /**
     * Undo is offered only while there is a row of the bot's own to take back.
     * A button that answers "nothing to undo" is worse than no button: it reads
     * as a broken feature rather than as nothing having happened yet.
     */
    public function test_undo_appears_only_once_the_bot_has_written_something(): void
    {
        $this->press('f:menu');

        $this->assertSame(
            [],
            array_filter($this->offeredCallbacks(), fn (string $data): bool => str_starts_with($data, 'f:undo')),
            'undo was offered with nothing to undo'
        );

        $this->message('ovqat 25000');
        $this->press('f:menu');

        $this->assertNotSame(
            [],
            array_filter($this->offeredCallbacks(), fn (string $data): bool => str_starts_with($data, 'f:undo')),
            'undo was not offered after the bot recorded a row'
        );
    }

    /**
     * The heading over a screen has to be written in the same alphabet as the
     * screen under it.
     *
     * Carbon files Uzbek under "uz" as Cyrillic and does not know "tj" at all,
     * so without a locale bridge the day heading read "Пайшанба" over a page of
     * Latin Uzbek, and every Tajik date fell back to English. Neither errors.
     */
    public function test_the_date_heading_matches_the_language_of_the_screen(): void
    {
        $expected = [
            'uz' => 'Payshanba',   // Latin, not "Пайшанба"
            'ru' => 'четверг',
            'en' => 'Thursday',
            'tj' => 'панҷшанбе',   // Tajik, not the English fallback
        ];

        foreach ($expected as $locale => $word) {
            app()->setLocale($locale);

            $this->assertSame(
                $word,
                \Carbon\CarbonImmutable::parse('2026-09-03')->translatedFormat('l'),
                "a {$locale} screen would be headed by the wrong alphabet"
            );
        }
    }

    /**
     * Refreshing a screen that has not changed is not a failure.
     *
     * Telegram refuses an edit that would leave the message exactly as it is.
     * Nothing is wrong — the screen is current — but logged as an API error it
     * arrives once per press and buries the refusals that do matter.
     */
    public function test_an_unchanged_screen_is_not_logged_as_a_failure(): void
    {
        $logged = $this->captureWarnings();

        $this->fakeTelegram([
            'ok' => false,
            'description' => 'Bad Request: message is not modified: specified new message content and reply markup are exactly the same as a current content and reply markup of the message',
        ], 400);

        $this->press('nav:day:' . now($this->user->timezone)->toDateString());

        $this->assertSame([], $logged->all(), 'an unchanged screen was logged as an API error');
    }

    /** The refusals that do matter still reach the log. */
    public function test_a_real_refusal_is_still_logged(): void
    {
        $logged = $this->captureWarnings();

        $this->fakeTelegram(['ok' => false, 'description' => 'Bad Request: chat not found'], 400);

        $this->press('nav:day:' . now($this->user->timezone)->toDateString());

        $this->assertNotSame([], $logged->all(), 'a real refusal went unlogged');
    }

    /**
     * Point every Telegram call at one canned answer, replacing whatever was
     * faked before.
     *
     * The reset matters: `Http::fake` appends, and stubs match in registration
     * order, so a per-test stub added behind the one from `setUp` never runs —
     * the test then passes on the setUp response and proves nothing. Forgetting
     * the factory first is what actually replaces it.
     *
     * @param  array<string, mixed>|null  $body
     */
    private function fakeTelegram(?array $body = null, int $status = 200): void
    {
        $this->app->forgetInstance(\Illuminate\Http\Client\Factory::class);
        Http::clearResolvedInstances();

        Http::fake([
            'api.telegram.org/*' => Http::response(
                $body ?? ['ok' => true, 'result' => ['message_id' => 1]],
                $status
            ),
        ]);
    }

    /** @return \Illuminate\Support\Collection<int, string> */
    private function captureWarnings(): \Illuminate\Support\Collection
    {
        $logged = collect();

        \Illuminate\Support\Facades\Log::listen(function ($event) use ($logged): void {
            if ($event->level === 'warning' && str_contains($event->message, 'Telegram API error')) {
                $logged->push($event->message);
            }
        });

        return $logged;
    }

    private function message(string $text): void
    {
        $this->bot->handleMessage([
            'chat' => ['id' => self::CHAT_ID],
            'from' => ['id' => self::CHAT_ID, 'language_code' => 'uz'],
            'text' => $text,
        ]);
    }

    private function press(string $data): void
    {
        $this->bot->handleCallback([
            'id' => '1',
            'data' => $data,
            'message' => ['message_id' => 1, 'chat' => ['id' => self::CHAT_ID]],
            'from' => ['id' => self::CHAT_ID],
        ]);
    }

    /**
     * Every callback_data the bot has drawn so far, in the order it drew them.
     *
     * @return list<string>
     */
    private function offeredCallbacks(): array
    {
        $found = [];

        foreach (Http::recorded() as [$request]) {
            $markup = json_decode((string) ($request->data()['reply_markup'] ?? ''), true);

            foreach ($markup['inline_keyboard'] ?? [] as $row) {
                foreach ($row as $button) {
                    if (isset($button['callback_data'])) {
                        $found[] = $button['callback_data'];
                    }
                }
            }
        }

        return $found;
    }
}
