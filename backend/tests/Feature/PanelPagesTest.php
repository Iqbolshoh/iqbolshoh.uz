<?php

namespace Tests\Feature;

use App\Enums\TransactionKind;
use App\Enums\NotificationKind;
use App\Models\FinanceCategory;
use App\Models\Goal;
use App\Models\Notification;
use App\Models\Plan;
use App\Models\User;
use App\Services\FinanceService;
use Carbon\CarbonImmutable;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Every page of the panel, with something on it.
 *
 * The list is taken from the router rather than written here, so a page added
 * later is covered the day it exists — a hand-kept list only ever proves that
 * the pages somebody remembered still work.
 *
 * Data matters as much as the route: an empty table renders every view happily,
 * and the two things this is really guarding against — a lazy-loaded relation
 * inside a loop, and a column read under a name the model does not have — only
 * appear once there are rows to loop over. `preventLazyLoading` is on outside
 * production, so an N+1 in a Blade file fails here rather than quietly costing
 * a hundred queries in front of the owner.
 */
class PanelPagesTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);

        $this->owner = User::query()->where('email', 'superadmin@iqbolshoh.uz')->firstOrFail();
        $this->owner->update(['timezone' => 'Asia/Samarkand']);

        $this->givePagesSomethingToShow();
    }

    public function test_every_panel_page_renders(): void
    {
        $checked = 0;

        foreach ($this->panelPages() as $path) {
            $response = $this->actingAs($this->owner)->get($path);

            // The exception goes in the message: a bare "500 is not 200" for a
            // page picked out of the router tells you nothing about which one
            // broke or why.
            $this->assertSame(200, $response->status(), sprintf(
                'GET /%s answered %d — %s',
                $path,
                $response->status(),
                $response->exception?->getMessage() ?? 'no exception recorded'
            ));

            $checked++;
        }

        $this->assertGreaterThan(10, $checked, 'the router gave back almost no pages, so this proved nothing');
    }

    /**
     * The panel's own GET pages: no parameters to guess at, and nothing that
     * logs out, downloads or redirects by design.
     *
     * @return list<string>
     */
    private function panelPages(): array
    {
        $skip = ['admin/login', 'admin/logout', 'admin', 'admin/transactions/export'];
        $pages = [];

        foreach (Route::getRoutes() as $route) {
            $uri = $route->uri();

            if (! str_starts_with($uri, 'admin') || ! in_array('GET', $route->methods(), true)) {
                continue;
            }

            if (str_contains($uri, '{') || in_array($uri, $skip, true)) {
                continue;
            }

            $pages[] = $uri;
        }

        return array_values(array_unique($pages));
    }

    private function givePagesSomethingToShow(): void
    {
        $today = CarbonImmutable::today($this->owner->timezone);

        app(FinanceService::class)->ensureDefaults($this->owner);

        $goal = Goal::query()->create([
            'user_id' => $this->owner->id,
            'title' => 'Ship the money side',
            'month' => $today->startOfMonth()->toDateString(),
        ]);

        foreach ([['09:00:00', 'completed'], ['11:30:00', 'pending'], ['16:00:00', 'failed']] as $i => [$time, $status]) {
            Plan::query()->create([
                'user_id' => $this->owner->id,
                'goal_id' => $goal->id,
                'title' => "Plan {$i}",
                'date' => $today->subDays($i)->toDateString(),
                'start_time' => $time,
                'planned_minutes' => 45,
                'status' => $status,
            ]);
        }

        $service = app(FinanceService::class);

        foreach (['food', 'transport', 'tech'] as $key) {
            $service->record(
                user: $this->owner,
                kind: TransactionKind::Expense,
                amount: 25000,
                category: FinanceCategory::query()->where('user_id', $this->owner->id)->where('key', $key)->first(),
            );
        }

        Notification::query()->create([
            'user_id' => $this->owner->id,
            'kind' => NotificationKind::Reminder,
            'sequence' => 0,
            'title' => 'A reminder',
            'body' => 'Something to show',
            'chat_id' => 1,
        ]);
    }
}
