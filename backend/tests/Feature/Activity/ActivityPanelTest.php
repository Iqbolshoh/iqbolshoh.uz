<?php

namespace Tests\Feature\Activity;

use App\Enums\ActivitySource;
use App\Models\ActivityCategory;
use App\Models\ActivityEntry;
use App\Models\User;
use App\Services\ActivityService;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The time pages of the panel, end to end.
 *
 * PanelPagesTest renders every page that takes no parameter, which leaves the
 * edit and delete routes untested — and those are exactly the ones where a
 * route-model binding silently stops matching. So the round trip is walked
 * here: create it, change it, remove it.
 */
class ActivityPanelTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);

        $this->owner = User::factory()->create(['timezone' => 'Asia/Samarkand']);
        $this->owner->assignRole('superadmin');

        app(ActivityService::class)->ensureDefaults($this->owner);

        $this->actingAs($this->owner);
    }

    public function test_an_entry_can_be_written_changed_and_removed(): void
    {
        $sleep = $this->category('sleep');
        $work = $this->category('work');

        $this->post(route('admin.activities-entries.store', absolute: false), [
            'category_id' => $sleep->id,
            'minutes' => 480,
            'date' => now($this->owner->timezone)->toDateString(),
            'note' => 'Went to bed early',
        ])->assertRedirect(route('admin.activities-entries.index', absolute: false));

        $entry = ActivityEntry::query()->firstOrFail();

        $this->assertSame(480, $entry->minutes);
        $this->assertSame(ActivitySource::Web, $entry->source);

        $this->get(route('admin.activities-entries.edit', $entry, absolute: false))->assertOk();

        $this->put(route('admin.activities-entries.update', $entry, absolute: false), [
            'category_id' => $work->id,
            'minutes' => 180,
            'date' => $entry->date->toDateString(),
        ])->assertRedirect(route('admin.activities-entries.index', absolute: false));

        $this->assertSame(180, $entry->fresh()->minutes);
        $this->assertSame($work->id, $entry->fresh()->category_id);

        $this->delete(route('admin.activities-entries.destroy', $entry, absolute: false))
            ->assertRedirect(route('admin.activities-entries.index', absolute: false));

        $this->assertSame(0, ActivityEntry::query()->count());
    }

    /** A day is the ceiling for one entry: past it it is a typo or a misread unit. */
    public function test_an_entry_longer_than_a_day_is_refused(): void
    {
        $this->post(route('admin.activities-entries.store', absolute: false), [
            'minutes' => ActivityService::DAY_MINUTES + 1,
            'date' => now()->toDateString(),
        ])->assertSessionHasErrors('minutes');

        $this->assertSame(0, ActivityEntry::query()->count());
    }

    /**
     * Deleting an activity keeps its hours. They were still spent; only the
     * label goes.
     */
    public function test_deleting_an_activity_keeps_its_entries(): void
    {
        $sleep = $this->category('sleep');

        $entry = app(ActivityService::class)->record(user: $this->owner, minutes: 420, category: $sleep);

        $this->delete(route('admin.activities-categories.destroy', $sleep, absolute: false))
            ->assertRedirect(route('admin.activities-categories.index', absolute: false));

        $entry->refresh();

        $this->assertSame(420, $entry->minutes);
        $this->assertNull($entry->category_id);
    }

    public function test_restore_defaults_tops_the_account_up(): void
    {
        $this->category('prayer')->delete();

        $this->post(route('admin.activities-categories.restore', absolute: false))
            ->assertRedirect(route('admin.activities-categories.index', absolute: false))
            ->assertSessionHas('success');

        $this->assertNotNull(
            ActivityCategory::query()->where('user_id', $this->owner->id)->where('key', 'prayer')->first()
        );
    }

    /** Another account's rows are never reachable, even with a valid id. */
    public function test_someone_elses_entry_is_out_of_reach(): void
    {
        $stranger = User::factory()->create();
        app(ActivityService::class)->ensureDefaults($stranger);

        $theirs = app(ActivityService::class)->record(user: $stranger, minutes: 60);

        $this->get(route('admin.activities-entries.edit', $theirs, absolute: false))->assertForbidden();
        $this->delete(route('admin.activities-entries.destroy', $theirs, absolute: false))->assertForbidden();
    }

    private function category(string $key): ActivityCategory
    {
        return ActivityCategory::query()
            ->where('user_id', $this->owner->id)
            ->where('key', $key)
            ->firstOrFail();
    }
}
