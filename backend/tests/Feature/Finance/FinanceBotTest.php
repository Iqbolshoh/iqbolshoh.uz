<?php

namespace Tests\Feature\Finance;

use App\Enums\TransactionSource;
use App\Models\FinanceCategory;
use App\Models\Transaction;
use App\Models\User;
use App\Services\FinanceBot;
use App\Services\FinanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * The money half of the bot, from a chat's point of view.
 *
 * Every Telegram call is faked with one catch-all stub. Stubs match in
 * registration order, so a per-test override has to be registered before this
 * one, never after — a later stub behind a catch-all never runs, and the test
 * passes for the wrong reason.
 */
class FinanceBotTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private FinanceBot $bot;

    private const CHAT_ID = 5339820458;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.telegram.token' => 'test-token']);

        Http::fake([
            'api.telegram.org/*' => Http::response(['ok' => true, 'result' => ['message_id' => 1]]),
        ]);

        $this->user = User::factory()->create(['timezone' => 'Asia/Samarkand']);
        app(FinanceService::class)->ensureDefaults($this->user);

        $this->bot = app(FinanceBot::class);
    }

    public function test_a_line_of_text_becomes_a_transaction(): void
    {
        $handled = $this->bot->handleText(self::CHAT_ID, $this->user, 'ovqat 25000');

        $this->assertTrue($handled);

        $transaction = Transaction::query()->firstOrFail();

        $this->assertSame(25000, $transaction->amount);
        $this->assertSame('expense', $transaction->kind->value);
        $this->assertSame(TransactionSource::Telegram, $transaction->source);
        $this->assertSame('food', $transaction->category->key);

        // Recorded on the owner's clock, not the server's.
        $this->assertSame(
            now('Asia/Samarkand')->toDateString(),
            $transaction->date->toDateString()
        );
    }

    /**
     * The day's own total, which is the number on nearly every screen.
     *
     * It is a `whereBetween` on the date column, and a `date` cast that writes
     * back a midnight timestamp makes that comparison miss every row — the
     * screens then read a confident zero rather than failing.
     */
    public function test_a_row_written_today_counts_towards_today(): void
    {
        $this->bot->handleText(self::CHAT_ID, $this->user, 'ovqat 25000');

        Http::assertSent(fn ($request) => str_contains(
            (string) ($request->data()['text'] ?? ''),
            Transaction::money(25000)
        ));

        $today = \Carbon\CarbonImmutable::today($this->user->timezone);

        $this->assertSame(
            25000,
            (int) Transaction::query()->where('user_id', $this->user->id)->between($today, $today)->sum('amount')
        );
    }

    public function test_text_without_an_amount_is_left_to_the_caller(): void
    {
        $this->assertFalse($this->bot->handleText(self::CHAT_ID, $this->user, 'salom'));
        $this->assertSame(0, Transaction::query()->count());
    }

    /**
     * An unrecognised word is still recorded — the amount is the part that
     * would otherwise be lost — and the bot asks where it belongs.
     */
    public function test_an_unknown_word_is_recorded_and_asked_about(): void
    {
        $this->bot->handleText(self::CHAT_ID, $this->user, 'qandolat 18000');

        $transaction = Transaction::query()->firstOrFail();

        $this->assertSame(18000, $transaction->amount);
        $this->assertNull($transaction->category_id);
        $this->assertSame('qandolat', $transaction->note);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'sendMessage')
                && str_contains((string) $request['reply_markup'], 'f:cat:');
        });
    }

    public function test_answering_the_question_files_the_row_and_teaches_the_word(): void
    {
        $this->bot->handleText(self::CHAT_ID, $this->user, 'qandolat 18000');

        $transaction = Transaction::query()->firstOrFail();
        $food = FinanceCategory::query()->where('key', 'food')->firstOrFail();

        $this->bot->handleCallback(self::CHAT_ID, 10, $this->user, ['f', 'cat', (string) $transaction->id, (string) $food->id]);

        $this->assertSame($food->id, $transaction->fresh()->category_id);
        $this->assertContains('qandolat', $food->fresh()->matchWords());
    }

    public function test_the_learned_word_is_used_next_time(): void
    {
        $this->bot->handleText(self::CHAT_ID, $this->user, 'qandolat 18000');
        $transaction = Transaction::query()->firstOrFail();
        $food = FinanceCategory::query()->where('key', 'food')->firstOrFail();

        $this->bot->handleCallback(self::CHAT_ID, 10, $this->user, ['f', 'cat', (string) $transaction->id, (string) $food->id]);

        $this->bot->handleText(self::CHAT_ID, $this->user, 'qandolat 5000');

        $this->assertSame($food->id, Transaction::query()->latest('id')->first()->category_id);
    }

    public function test_undo_removes_a_row_the_bot_added(): void
    {
        $this->bot->handleText(self::CHAT_ID, $this->user, 'taksi 12k');
        $transaction = Transaction::query()->firstOrFail();

        $this->bot->handleCallback(self::CHAT_ID, 10, $this->user, ['f', 'undo', (string) $transaction->id]);

        $this->assertSame(0, Transaction::query()->count());
    }

    /**
     * Undo must not reach a row typed into the admin panel: the owner can see
     * and delete those there, and a bot button that silently removes them is a
     * way to lose data without noticing.
     */
    public function test_undo_will_not_touch_a_row_from_the_panel(): void
    {
        $transaction = Transaction::query()->create([
            'user_id' => $this->user->id,
            'kind' => 'expense',
            'amount' => 9000,
            'date' => now('Asia/Samarkand')->toDateString(),
            'method' => 'cash',
            'source' => TransactionSource::Web->value,
        ]);

        $this->bot->handleCallback(self::CHAT_ID, 10, $this->user, ['f', 'undo', (string) $transaction->id]);

        $this->assertDatabaseHas('transactions', ['id' => $transaction->id]);
    }

    public function test_a_limit_crossing_is_warned_about_once(): void
    {
        $food = FinanceCategory::query()->where('key', 'food')->firstOrFail();
        $food->update(['monthly_limit' => 100000]);

        // 70% — under the 80% threshold, so nothing is said.
        $this->bot->handleText(self::CHAT_ID, $this->user, 'ovqat 70000');
        $this->assertNoWarningSent();

        // Crosses it.
        $this->bot->handleText(self::CHAT_ID, $this->user, 'ovqat 20000');
        $this->assertWarningSent();
    }

    private function assertWarningSent(): void
    {
        Http::assertSent(fn ($request) => str_contains((string) $request['text'], '⚠️'));
    }

    private function assertNoWarningSent(): void
    {
        Http::assertNotSent(fn ($request) => str_contains((string) $request['text'], '⚠️'));
    }
}
