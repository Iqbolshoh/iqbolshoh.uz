<?php

namespace Tests\Feature\Telegram;

use App\Models\Plan;
use App\Models\TelegramAccount;
use App\Models\User;
use App\Services\ActivityService;
use App\Services\FinanceService;
use App\Services\TelegramBot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * The one screen that answers "how did it go".
 *
 * Tasks, money and time used to be three separate trips, and nobody makes the
 * third. They are one message now, and the rule this guards is that all three
 * sections are always there — an empty one says so rather than disappearing,
 * because a section that vanishes reads as a broken feature and hides the fact
 * that there is nothing to see.
 */
class ReportTest extends TestCase
{
    use RefreshDatabase;

    private const CHAT_ID = 5339820458;

    private User $user;

    private TelegramBot $bot;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.telegram.token' => 'test-token']);

        Http::fake(['api.telegram.org/*' => Http::response(['ok' => true, 'result' => ['message_id' => 1]])]);

        $this->user = User::factory()->create(['timezone' => 'Asia/Samarkand']);

        TelegramAccount::query()->create([
            'user_id' => $this->user->id,
            'telegram_id' => self::CHAT_ID,
            'is_active' => true,
        ]);

        app(FinanceService::class)->ensureDefaults($this->user);
        app(ActivityService::class)->ensureDefaults($this->user);

        $this->bot = app(TelegramBot::class);
    }

    public function test_the_report_carries_tasks_money_and_time(): void
    {
        $this->plan('Ship the release', 'completed');
        $this->plan('Send the invoice', 'failed');

        $this->say('taksi 12000');
        $this->say('8 soat uxladim');

        $text = $this->reportText();

        $this->assertStringContainsString(__('bot.report.tasks'), $text);
        $this->assertStringContainsString(__('bot.report.money'), $text);
        $this->assertStringContainsString(__('bot.report.time'), $text);

        // The numbers themselves, not just the headings.
        $this->assertStringContainsString('50%', $text, 'one of two settled plans is half');
        $this->assertStringContainsString('Taksi', $text);
        $this->assertStringContainsString('Uyqu', $text);
    }

    /** A day with nothing in it still shows all three sections. */
    public function test_an_empty_section_says_so_instead_of_disappearing(): void
    {
        $text = $this->reportText();

        $this->assertStringContainsString(__('bot.report.tasks_none'), $text);
        $this->assertStringContainsString(__('bot.report.money_none'), $text);
        $this->assertStringContainsString(__('bot.report.time_none'), $text);
    }

    /**
     * Income only earns a line when there is some: a permanent "0 so'm" trains
     * the eye to skip the block it sits in.
     */
    public function test_income_is_shown_only_when_there_is_any(): void
    {
        $this->say('taksi 12000');

        $this->assertStringNotContainsString(__('bot.report.money_in', ['amount' => '']), $this->reportText());

        $this->say('+oylik 5 mln');

        $this->assertStringContainsString('🔺', $this->reportText());
    }

    public function test_the_period_buttons_switch_the_report(): void
    {
        $this->assertStringContainsString(__('bot.report.title_today'), $this->reportText());

        $this->bot->handleCallback([
            'message' => ['chat' => ['id' => self::CHAT_ID], 'message_id' => 7],
            'data' => 'nav:stats:week',
        ]);

        $this->assertStringContainsString(__('bot.report.title_week'), $this->lastText());

        $this->bot->handleCallback([
            'message' => ['chat' => ['id' => self::CHAT_ID], 'message_id' => 7],
            'data' => 'nav:stats:month',
        ]);

        $this->assertStringContainsString(__('bot.report.title_month'), $this->lastText());
    }

    /**
     * A plan whose time has not come yet is not a failure.
     *
     * Counting it against the rate would drag every morning's number to the
     * floor and make the one figure on the screen useless before lunch.
     */
    public function test_a_pending_plan_is_counted_apart_from_the_rate(): void
    {
        $this->plan('Done already', 'completed');
        $this->plan('Not yet', 'pending');

        $text = $this->reportText();

        $this->assertStringContainsString('100%', $text);
        $this->assertStringContainsString(__('bot.report.tasks_pending', ['count' => 1]), $text);
    }

    private function plan(string $title, string $status): void
    {
        Plan::query()->create([
            'user_id' => $this->user->id,
            'title' => $title,
            'date' => now($this->user->timezone)->toDateString(),
            'start_time' => '09:00:00',
            'planned_minutes' => 30,
            'status' => $status,
        ]);
    }

    private function say(string $text): void
    {
        $this->bot->handleMessage([
            'chat' => ['id' => self::CHAT_ID],
            'text' => $text,
            'from' => ['language_code' => 'uz'],
        ]);
    }

    private function reportText(): string
    {
        $this->say('/stats');

        return $this->lastText();
    }

    private function lastText(): string
    {
        return (string) Http::recorded()
            ->filter(fn (array $pair): bool => isset($pair[0]->data()['text']))
            ->last()[0]['text'];
    }
}
