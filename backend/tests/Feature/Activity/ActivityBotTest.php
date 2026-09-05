<?php

namespace Tests\Feature\Activity;

use App\Enums\ActivitySource;
use App\Enums\InterruptionType;
use App\Models\ActivityCategory;
use App\Models\ActivityEntry;
use App\Models\Interruption;
use App\Models\User;
use App\Services\ActivityBot;
use App\Services\ActivityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * The time half of the bot, from a chat's point of view.
 *
 * Every Telegram call is faked with one catch-all stub. Stubs match in
 * registration order, so a per-test override has to be registered before this
 * one — a later stub behind a catch-all never runs and the test passes for the
 * wrong reason.
 */
class ActivityBotTest extends TestCase
{
    use RefreshDatabase;

    private const CHAT_ID = 5339820458;

    private User $user;

    private ActivityBot $bot;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.telegram.token' => 'test-token']);

        Http::fake([
            'api.telegram.org/*' => Http::response(['ok' => true, 'result' => ['message_id' => 1]]),
        ]);

        $this->user = User::factory()->create(['timezone' => 'Asia/Samarkand']);
        app(ActivityService::class)->ensureDefaults($this->user);

        $this->bot = app(ActivityBot::class);
    }

    public function test_a_line_of_text_becomes_an_entry(): void
    {
        $this->assertTrue($this->bot->handleText(self::CHAT_ID, $this->user, '8 soat uxladim'));

        $entry = ActivityEntry::query()->firstOrFail();

        $this->assertSame(480, $entry->minutes);
        $this->assertSame('sleep', $entry->category->key);
        $this->assertSame(ActivitySource::Telegram, $entry->source);

        // Recorded on the owner's clock, not the server's.
        $this->assertSame(now('Asia/Samarkand')->toDateString(), $entry->date->toDateString());
    }

    public function test_text_without_a_duration_is_left_to_the_caller(): void
    {
        $this->assertFalse($this->bot->handleText(self::CHAT_ID, $this->user, 'ovqat 25000'));
        $this->assertSame(0, ActivityEntry::query()->count());
    }

    /** Buttons, then a number — the same two taps as the money side. */
    public function test_time_can_be_logged_with_buttons_and_a_number(): void
    {
        $sport = ActivityCategory::query()->where('key', 'sport')->firstOrFail();

        $this->bot->handleCallback(self::CHAT_ID, 10, $this->user, ['t', 'add']);
        $this->bot->handleCallback(self::CHAT_ID, 10, $this->user, ['t', 'new', (string) $sport->id]);
        $this->bot->handleText(self::CHAT_ID, $this->user, '45');

        $entry = ActivityEntry::query()->firstOrFail();

        $this->assertSame(45, $entry->minutes);
        $this->assertSame($sport->id, $entry->category_id);
    }

    public function test_leaving_the_flow_abandons_the_half_finished_entry(): void
    {
        $sport = ActivityCategory::query()->where('key', 'sport')->firstOrFail();

        $this->bot->handleCallback(self::CHAT_ID, 10, $this->user, ['t', 'new', (string) $sport->id]);
        $this->bot->handleCallback(self::CHAT_ID, 10, $this->user, ['t', 'menu']);

        $this->assertFalse($this->bot->handleText(self::CHAT_ID, $this->user, '45'));
        $this->assertSame(0, ActivityEntry::query()->count());
    }

    public function test_an_unknown_word_is_recorded_and_asked_about(): void
    {
        $this->bot->handleText(self::CHAT_ID, $this->user, '2 soat bogdorchilik');

        $entry = ActivityEntry::query()->firstOrFail();

        $this->assertSame(120, $entry->minutes);
        $this->assertNull($entry->category_id);
        $this->assertSame('bogdorchilik', $entry->note);

        Http::assertSent(fn ($request) => str_contains($request->url(), 'sendMessage')
            && str_contains((string) $request['reply_markup'], 't:cat:'));
    }

    public function test_answering_the_question_files_the_entry_and_teaches_the_word(): void
    {
        $this->bot->handleText(self::CHAT_ID, $this->user, '2 soat bogdorchilik');

        $entry = ActivityEntry::query()->firstOrFail();
        $chores = ActivityCategory::query()->where('key', 'chores')->firstOrFail();

        $this->bot->handleCallback(self::CHAT_ID, 10, $this->user, ['t', 'cat', (string) $entry->id, (string) $chores->id]);

        $this->assertSame($chores->id, $entry->fresh()->category_id);
        $this->assertContains('bogdorchilik', $chores->fresh()->matchWords());
    }

    public function test_undo_removes_an_entry_the_bot_added(): void
    {
        $this->bot->handleText(self::CHAT_ID, $this->user, '8 soat uxladim');

        $entry = ActivityEntry::query()->firstOrFail();

        $this->bot->handleCallback(self::CHAT_ID, 10, $this->user, ['t', 'undo', (string) $entry->id]);

        $this->assertSame(0, ActivityEntry::query()->count());
    }

    /**
     * Undo must not reach an entry the bot derived from a status.
     *
     * The owner did not type it and would not expect it to vanish; it is a
     * measurement, and the place to remove one is the panel.
     */
    public function test_undo_will_not_touch_an_entry_derived_from_a_status(): void
    {
        $entry = app(ActivityService::class)->record(
            user: $this->user,
            minutes: 60,
            source: ActivitySource::Status,
        );

        $this->bot->handleCallback(self::CHAT_ID, 10, $this->user, ['t', 'undo', (string) $entry->id]);

        $this->assertSame(1, ActivityEntry::query()->count());
    }

    /**
     * A status the owner set is hours they already reported once.
     *
     * Asking them to type it again is the reason a time log stops being filled
     * in after a week.
     */
    public function test_a_finished_status_becomes_a_time_entry(): void
    {
        $interruption = Interruption::query()->create([
            'user_id' => $this->user->id,
            'type' => InterruptionType::Class_->value,
            'title' => 'Algebra',
            'started_at' => now()->subHours(2),
            'ends_at' => now(),
            'ended_at' => now(),
            'duration_minutes' => 120,
        ]);

        app(ActivityService::class)->recordInterruption($interruption);

        $entry = ActivityEntry::query()->firstOrFail();

        $this->assertSame(120, $entry->minutes);
        $this->assertSame('study', $entry->category->key);
        $this->assertSame(ActivitySource::Status, $entry->source);
        $this->assertSame('Algebra', $entry->note);
    }

    /**
     * The scheduler closes a status on a timer and the owner can close the
     * same one by hand a second later. Two calls must not become two entries.
     */
    public function test_the_same_status_is_never_logged_twice(): void
    {
        $interruption = Interruption::query()->create([
            'user_id' => $this->user->id,
            'type' => 'work',
            'started_at' => now()->subHour(),
            'ends_at' => now(),
            'ended_at' => now(),
            'duration_minutes' => 60,
        ]);

        $service = app(ActivityService::class);

        $this->assertNotNull($service->recordInterruption($interruption));
        $this->assertNull($service->recordInterruption($interruption));
        $this->assertSame(1, ActivityEntry::query()->count());
    }

    /** An emergency has no honest activity to become, so it becomes none. */
    public function test_a_status_with_no_matching_activity_is_left_alone(): void
    {
        $interruption = Interruption::query()->create([
            'user_id' => $this->user->id,
            'type' => 'emergency',
            'started_at' => now()->subHour(),
            'ended_at' => now(),
            'duration_minutes' => 60,
        ]);

        $this->assertNull(app(ActivityService::class)->recordInterruption($interruption));
        $this->assertSame(0, ActivityEntry::query()->count());
    }
}
