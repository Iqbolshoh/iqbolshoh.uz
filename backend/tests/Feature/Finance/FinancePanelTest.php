<?php

namespace Tests\Feature\Finance;

use App\Enums\TransactionKind;
use App\Models\FinanceCategory;
use App\Models\Transaction;
use App\Models\User;
use App\Services\FinanceService;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The finance pages, from the owner's side.
 *
 * Every one of these makes a request, so RefreshDatabase is not optional: a
 * request-making test without it leaks its rows into whichever class runs
 * next, and the failure surfaces somewhere else entirely.
 */
class FinancePanelTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);

        $this->owner = User::query()->where('email', 'superadmin@iqbolshoh.uz')->firstOrFail();
        $this->owner->update(['timezone' => 'Asia/Samarkand']);

        app(FinanceService::class)->ensureDefaults($this->owner);
    }

    public function test_every_finance_page_renders(): void
    {
        foreach ([
            '/admin/finance',
            '/admin/transactions',
            '/admin/transactions/create',
            '/admin/finance-categories',
            '/admin/finance-categories/create',
            '/admin/finance-settings',
        ] as $path) {
            $this->actingAs($this->owner)
                ->get($path)
                ->assertOk();
        }
    }

    public function test_a_transaction_can_be_recorded_and_shows_in_the_ledger(): void
    {
        $category = FinanceCategory::query()->where('key', 'food')->firstOrFail();

        $this->actingAs($this->owner)
            ->post('/admin/transactions', [
                'kind' => 'expense',
                'amount' => 25000,
                'category_id' => $category->id,
                'date' => '2026-09-03',
                'time' => '13:40',
                'method' => 'cash',
                'note' => 'tushlik',
            ])
            ->assertRedirect('/admin/transactions');

        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->owner->id,
            'amount' => 25000,
            'kind' => 'expense',
            'source' => 'web',
        ]);
    }

    /**
     * A category of the wrong direction must not stick. Without this the panel
     * would happily file an expense under Salary, and every report built on
     * `kind` would disagree with every report built on the category.
     */
    public function test_a_category_of_the_wrong_direction_is_dropped(): void
    {
        $salary = FinanceCategory::query()->where('key', 'salary')->firstOrFail();

        $this->actingAs($this->owner)->post('/admin/transactions', [
            'kind' => 'expense',
            'amount' => 10000,
            'category_id' => $salary->id,
            'date' => '2026-09-03',
            'method' => 'cash',
        ]);

        $this->assertSame(null, Transaction::query()->latest('id')->first()->category_id);
    }

    public function test_another_account_cannot_touch_these_rows(): void
    {
        $intruder = User::factory()->create();
        $transaction = Transaction::query()->create([
            'user_id' => $this->owner->id,
            'kind' => TransactionKind::Expense->value,
            'amount' => 5000,
            'date' => '2026-09-03',
            'method' => 'cash',
            'source' => 'web',
        ]);

        $this->actingAs($intruder)
            ->delete("/admin/transactions/{$transaction->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('transactions', ['id' => $transaction->id]);
    }

    public function test_the_csv_export_carries_the_filtered_rows(): void
    {
        Transaction::query()->create([
            'user_id' => $this->owner->id,
            'kind' => TransactionKind::Expense->value,
            'amount' => 77000,
            'date' => now('Asia/Samarkand')->toDateString(),
            'method' => 'card',
            'source' => 'web',
            'note' => 'server uchun',
        ]);

        $response = $this->actingAs($this->owner)->get('/admin/transactions/export?period=month');
        $response->assertOk();

        $csv = $response->streamedContent();

        $this->assertStringContainsString('77000', $csv);
        $this->assertStringContainsString('server uchun', $csv);
    }
}
