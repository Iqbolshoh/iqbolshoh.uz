<?php

namespace Tests\Feature\Activity;

use App\Models\ActivityCategory;
use App\Models\User;
use App\Services\ActivityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Lang;
use ReflectionClass;
use Tests\TestCase;

/**
 * The list of ways time can be spent.
 *
 * The same two rules as the finance catalogue, checked the same way and for
 * the same reason: the list is long enough that reading it proves nothing.
 */
class ActivityCatalogueTest extends TestCase
{
    use RefreshDatabase;

    private const LOCALES = ['uz', 'ru', 'en', 'tj'];

    public function test_every_default_activity_is_named_in_every_language(): void
    {
        foreach (self::LOCALES as $locale) {
            $named = array_keys(Lang::get('activity.categories', [], $locale));

            $this->assertSame([], array_diff($this->defaultKeys(), $named),
                "lang/{$locale}/activity.php does not name every default activity");

            $this->assertSame([], array_diff($named, $this->defaultKeys()),
                "lang/{$locale}/activity.php names an activity that is not seeded");
        }
    }

    /**
     * A word may answer for exactly one activity.
     *
     * Two claiming "dars" is not a tie the parser can break: it keeps the
     * first longest match, so the winner is whichever was seeded earlier and
     * the loser is unreachable from the bot forever, while every test passes.
     */
    public function test_no_word_answers_for_two_activities(): void
    {
        $seen = [];
        $clashes = [];

        foreach ($this->defaults() as $definition) {
            foreach (explode(',', $definition['keywords']) as $word) {
                $word = trim(mb_strtolower($word));

                if (isset($seen[$word])) {
                    $clashes[] = "“{$word}” is claimed by both {$seen[$word]} and {$definition['key']}";
                }

                $seen[$word] = $definition['key'];
            }
        }

        $this->assertSame([], $clashes);
    }

    /** Every activity carrying a daily target must have a plausible one. */
    public function test_a_daily_target_fits_inside_a_day(): void
    {
        foreach ($this->defaults() as $definition) {
            $target = $definition['daily_target_minutes'];

            if ($target === null) {
                continue;
            }

            $this->assertGreaterThan(0, $target, "{$definition['key']} has a target of zero");
            $this->assertLessThanOrEqual(
                ActivityService::DAY_MINUTES,
                $target,
                "{$definition['key']} wants more than a day"
            );
        }
    }

    public function test_sync_creates_what_is_missing_and_is_a_no_op_after_that(): void
    {
        $user = User::factory()->create();
        $service = app(ActivityService::class);

        $this->assertSame(count($this->defaults()), $service->syncDefaults($user)['created']);

        $again = $service->syncDefaults($user);

        $this->assertSame(0, $again['created']);
        $this->assertSame(0, $again['updated']);
    }

    /** A word the bot learned from a correction survives every sync. */
    public function test_sync_keeps_a_word_the_bot_learned(): void
    {
        $user = User::factory()->create();
        $service = app(ActivityService::class);
        $service->syncDefaults($user);

        $work = ActivityCategory::query()->where('user_id', $user->id)->where('key', 'work')->firstOrFail();
        $work->update(['keywords' => $work->keywords . ',deploy qildim']);

        $service->syncDefaults($user);

        $this->assertContains('deploy qildim', $work->fresh()->matchWords());
    }

    /** The picker leads with what this person actually logs. */
    public function test_activities_are_ordered_by_how_often_they_are_logged(): void
    {
        $user = User::factory()->create();
        $service = app(ActivityService::class);
        $service->syncDefaults($user);

        $prayer = ActivityCategory::query()->where('user_id', $user->id)->where('key', 'prayer')->firstOrFail();

        $service->record(user: $user, minutes: 20, category: $prayer);

        $this->assertSame($prayer->id, $service->categoriesByUse($user)->first()->id);
    }

    /** @return array<int, array{key: string, keywords: string, daily_target_minutes: ?int}> */
    private function defaults(): array
    {
        return (new ReflectionClass(ActivityService::class))->getConstant('DEFAULT_CATEGORIES');
    }

    /** @return list<string> */
    private function defaultKeys(): array
    {
        return array_column($this->defaults(), 'key');
    }
}
