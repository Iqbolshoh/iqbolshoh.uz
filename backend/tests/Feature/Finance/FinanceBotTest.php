<?php

namespace Tests\Feature\Finance;

use App\Enums\TransactionKind;
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

    /**
     * Money can be written down without typing anything but the number.
     *
     * This is the path the owner asked for after pressing "Pul" and finding
     * nothing to press: the screen reported what had been spent and offered no
     * way to add to it, and the one way in — writing "ovqat 25000" — was
     * documented nowhere they would look while standing at a counter.
     */
    public function test_money_can_be_written_with_buttons_and_a_number(): void
    {
        $taxi = FinanceCategory::query()->where('key', 'taxi')->firstOrFail();

        $this->bot->handleCallback(self::CHAT_ID, 10, $this->user, ['f', 'add', 'expense']);

        $this->assertContains("f:new:{$taxi->id}", $this->buttonsOfLastMessage());

        $this->bot->handleCallback(self::CHAT_ID, 10, $this->user, ['f', 'new', (string) $taxi->id]);
        $this->bot->handleText(self::CHAT_ID, $this->user, '25000');

        $transaction = Transaction::query()->firstOrFail();

        $this->assertSame(25000, $transaction->amount);
        $this->assertSame($taxi->id, $transaction->category_id);
        $this->assertSame('expense', $transaction->kind->value);
    }

    /** The same two taps, the other direction. */
    public function test_income_can_be_written_with_buttons_too(): void
    {
        $salary = FinanceCategory::query()->where('key', 'salary')->firstOrFail();

        $this->bot->handleCallback(self::CHAT_ID, 10, $this->user, ['f', 'add', 'income']);
        $this->bot->handleCallback(self::CHAT_ID, 10, $this->user, ['f', 'new', (string) $salary->id]);
        $this->bot->handleText(self::CHAT_ID, $this->user, '5 mln');

        $transaction = Transaction::query()->firstOrFail();

        $this->assertSame(5000000, $transaction->amount);
        $this->assertSame('income', $transaction->kind->value);
        $this->assertSame($salary->id, $transaction->category_id);
    }

    /**
     * A small bare number is only money while the bot is asking for one.
     *
     * Free text keeps its floor — that is what stops "ertalab 8 da yugurish"
     * becoming an eight som expense — but the question "how much?" was asked
     * one message ago, so there is nothing left to guess wrong.
     */
    public function test_a_small_number_counts_only_as_an_answer_to_the_question(): void
    {
        $this->assertFalse($this->bot->handleText(self::CHAT_ID, $this->user, '15'));
        $this->assertSame(0, Transaction::query()->count());

        $cafe = FinanceCategory::query()->where('key', 'cafe')->firstOrFail();

        $this->bot->handleCallback(self::CHAT_ID, 10, $this->user, ['f', 'new', (string) $cafe->id]);

        $this->assertTrue($this->bot->handleText(self::CHAT_ID, $this->user, '15'));
        $this->assertSame(15, Transaction::query()->firstOrFail()->amount);
    }

    /**
     * Walking away from a half-finished entry abandons it.
     *
     * Otherwise a category tapped and forgotten would quietly collect the next
     * bare number typed for some other reason entirely.
     */
    public function test_leaving_the_flow_abandons_the_half_finished_entry(): void
    {
        $cafe = FinanceCategory::query()->where('key', 'cafe')->firstOrFail();

        $this->bot->handleCallback(self::CHAT_ID, 10, $this->user, ['f', 'new', (string) $cafe->id]);
        $this->bot->handleCallback(self::CHAT_ID, 10, $this->user, ['f', 'menu']);

        $this->assertFalse($this->bot->handleText(self::CHAT_ID, $this->user, '15'));
        $this->assertSame(0, Transaction::query()->count());
    }

    /**
     * A line that names its own category beats the one that was tapped: the
     * person changed their mind while typing, and the words are the evidence.
     */
    public function test_a_named_category_outranks_the_one_that_was_tapped(): void
    {
        $cafe = FinanceCategory::query()->where('key', 'cafe')->firstOrFail();

        $this->bot->handleCallback(self::CHAT_ID, 10, $this->user, ['f', 'new', (string) $cafe->id]);
        $this->bot->handleText(self::CHAT_ID, $this->user, 'taksi 12000');

        $this->assertSame('taxi', Transaction::query()->firstOrFail()->category->key);
    }

    /** The money screen's own "today", which used to open the plans day. */
    public function test_today_on_the_money_screen_is_about_money(): void
    {
        $this->bot->handleText(self::CHAT_ID, $this->user, 'taksi 12000');
        $this->bot->handleCallback(self::CHAT_ID, 10, $this->user, ['f', 'today']);

        Http::assertSent(fn ($request) => str_contains($request->url(), 'editMessageText')
            && str_contains((string) $request['text'], Transaction::money(12000))
            && str_contains((string) $request['text'], 'Taksi'));
    }

    /**
     * The picker offers a shortlist, not the whole catalogue.
     *
     * There are three dozen categories. Drawing all of them is not a choice
     * offered to somebody standing at a counter — it is a wall to scroll past,
     * and the fastest way to make them stop using the bot.
     */
    public function test_the_picker_offers_a_shortlist_and_a_way_to_the_rest(): void
    {
        $this->bot->handleText(self::CHAT_ID, $this->user, 'qandolat 18000');

        $transaction = Transaction::query()->firstOrFail();
        $offered = $this->buttonsOfLastMessage();

        $categoryButtons = array_filter($offered, fn (string $data): bool => str_starts_with($data, 'f:cat:'));

        $this->assertCount(8, $categoryButtons);
        $this->assertContains("f:pick:{$transaction->id}:all", $offered);

        // ...and the way to the rest really does reach the rest.
        $this->bot->handleCallback(self::CHAT_ID, 10, $this->user, ['f', 'pick', (string) $transaction->id, 'all']);

        $all = array_filter($this->buttonsOfLastMessage(), fn (string $data): bool => str_starts_with($data, 'f:cat:'));

        $this->assertCount(
            app(FinanceService::class)->categoriesByUse($this->user, TransactionKind::Expense)->count(),
            $all
        );
    }

    /**
     * A guess that went to the wrong bucket can be moved.
     *
     * Without this the only repair was to delete the row and type the line
     * again — which is why a wrong guess used to cost more than no guess.
     */
    public function test_a_categorised_row_can_be_moved_to_another_category(): void
    {
        $this->bot->handleText(self::CHAT_ID, $this->user, 'ovqat 25000');

        $transaction = Transaction::query()->firstOrFail();
        $this->assertSame('food', $transaction->category->key);

        $this->bot->handleCallback(self::CHAT_ID, 10, $this->user, ['f', 'pick', (string) $transaction->id]);

        $this->assertContains("f:cat:{$transaction->id}:", array_map(
            fn (string $data): string => substr($data, 0, strrpos($data, ':') + 1),
            $this->buttonsOfLastMessage()
        ));

        $cafe = FinanceCategory::query()->where('key', 'cafe')->firstOrFail();

        $this->bot->handleCallback(self::CHAT_ID, 11, $this->user, ['f', 'cat', (string) $transaction->id, (string) $cafe->id]);

        $this->assertSame($cafe->id, $transaction->fresh()->category_id);
    }

    /**
     * Whatever else was typed on the line comes back on the screen.
     *
     * A row that reads "40 000 · Uncategorised" a week later cannot be placed
     * by anyone; the same row with "oldirdim" under it can.
     */
    public function test_the_note_is_shown_back(): void
    {
        $this->bot->handleText(self::CHAT_ID, $this->user, 'taksi 12000 aeroportgacha');

        Http::assertSent(fn ($request) => str_contains($request->url(), 'sendMessage')
            && str_contains((string) $request['text'], 'aeroportgacha'));
    }

    /** The buttons on the message the bot sent last, as callback data. */
    private function buttonsOfLastMessage(): array
    {
        $markup = json_decode((string) Http::recorded()->last()[0]['reply_markup'], true);

        return array_merge(...array_map(
            fn (array $row): array => array_column($row, 'callback_data'),
            $markup['inline_keyboard'] ?? []
        ));
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
