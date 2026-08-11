<?php

namespace Database\Seeders;

use App\Enums\FailReason;
use App\Enums\GoalStatus;
use App\Enums\InterruptionType;
use App\Enums\NotificationKind;
use App\Enums\NotificationStatus;
use App\Enums\PlanEventType;
use App\Enums\PlanStatus;
use App\Enums\PostponeReason;
use App\Enums\Priority;
use App\Models\Goal;
use App\Models\Interruption;
use App\Models\Notification;
use App\Models\Plan;
use App\Models\PlanEvent;
use App\Models\PlanSetting;
use App\Models\Project;
use App\Models\TelegramAccount;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Two months of plausible history, so the analytics, calendar and forecast
 * pages have something real to draw the moment the panel is opened.
 *
 * The data is not uniform noise. It carries the patterns the forecast is
 * supposed to find — mornings go well, evenings do not, personal time slips
 * first, and a handful of days were eaten by meetings — because a forecast page
 * fed random numbers proves nothing about whether the forecast works.
 */
class PlanSeeder extends Seeder
{
    /** Completion odds by hour band; the shape the recommendation engine should discover. */
    private const HOUR_BIAS = [
        'morning' => 0.87,   // 06:00–13:00
        'afternoon' => 0.74, // 13:00–18:00
        'evening' => 0.52,   // 18:00–23:00
    ];

    public function run(): void
    {
        $user = User::query()->orderBy('id')->first();

        if (! $user) {
            $this->command?->warn('No user to seed plans for.');

            return;
        }

        // Re-running the seeder replaces the demo history rather than stacking a
        // second copy on top of it.
        DB::transaction(function () use ($user) {
            Notification::query()->where('user_id', $user->id)->delete();
            PlanEvent::query()->whereIn('plan_id', Plan::query()->where('user_id', $user->id)->select('id'))->delete();
            Plan::query()->where('user_id', $user->id)->delete();
            Interruption::query()->where('user_id', $user->id)->delete();
            Goal::query()->where('user_id', $user->id)->delete();
        });

        PlanSetting::query()->updateOrCreate(['user_id' => $user->id], []);

        if ($chatId = config('services.telegram.chat_id')) {
            TelegramAccount::query()->updateOrCreate(
                ['telegram_id' => (int) $chatId],
                [
                    'user_id' => $user->id,
                    'username' => config('services.telegram.bot_username'),
                    'first_name' => $user->name,
                    'is_active' => true,
                    'linked_at' => now(),
                ]
            );
        }

        // Deterministic: the same history every time the seeder runs, which
        // makes a screenshot or a bug report reproducible.
        mt_srand(20260811);

        $today = CarbonImmutable::now($user->timezone)->startOfDay();
        $start = $today->subMonth()->startOfMonth();

        $goals = $this->seedGoals($user->id, $start, $today);
        $interruptions = $this->seedInterruptions($user->id, $start, $today);

        $this->seedPlans($user, $goals, $interruptions, $start, $today);
        $this->seedSiteNotifications($user->id, $today);

        $this->command?->info('✓ Plan demo history seeded.');
        $this->command?->info('  goals: ' . Goal::query()->where('user_id', $user->id)->count());
        $this->command?->info('  plans: ' . Plan::query()->where('user_id', $user->id)->count());
        $this->command?->info('  events: ' . PlanEvent::query()->count());
    }

    /**
     * @return array<string, list<Goal>> keyed by month, "Y-m"
     */
    private function seedGoals(int $userId, CarbonImmutable $start, CarbonImmutable $today): array
    {
        $templates = [
            ['English', 'Reading, listening and speaking practice for IELTS.', '30 hours', Priority::High, '#0EA5E9'],
            ['Projects', 'Ship and maintain the work in the portfolio.', '60 hours', Priority::High, '#8B5CF6'],
            ['Personal', 'Reading, sport and time away from the screen.', '20 hours', Priority::Medium, '#22C55E'],
        ];

        $byMonth = [];
        $month = $start->startOfMonth();

        while ($month->lessThanOrEqualTo($today->startOfMonth())) {
            $isPast = $month->lessThan($today->startOfMonth());

            foreach ($templates as $index => [$title, $description, $target, $priority, $color]) {
                $byMonth[$month->format('Y-m')][] = Goal::query()->create([
                    'user_id' => $userId,
                    'month' => $month->toDateString(),
                    'title' => $title,
                    'description' => $description,
                    'target' => $target,
                    'priority' => $priority,
                    // Last month is settled — "Personal" is the one that slipped,
                    // which is what makes the goal breakdown worth reading.
                    'status' => $isPast
                        ? ($index === 2 ? GoalStatus::Missed : GoalStatus::Achieved)
                        : GoalStatus::Active,
                    'color' => $color,
                    'sort_order' => $index,
                ]);
            }

            $month = $month->addMonth();
        }

        return $byMonth;
    }

    /** @return list<Interruption> */
    private function seedInterruptions(int $userId, CarbonImmutable $start, CarbonImmutable $today): array
    {
        $entries = [
            [$start->addDays(9)->setTime(10, 0), 120, InterruptionType::Meeting, 'Client call that ran long'],
            [$start->addDays(17)->setTime(14, 30), 180, InterruptionType::Travel, 'Trip to Samarkand'],
            [$start->addDays(24)->setTime(9, 0), 240, InterruptionType::Class_, 'Exam session'],
            [$today->subDays(6)->setTime(16, 0), 90, InterruptionType::Guest, 'Guests at home'],
            [$today->subDays(2)->setTime(11, 0), 150, InterruptionType::Emergency, 'Production incident'],
        ];

        $interruptions = [];

        foreach ($entries as [$startedAt, $minutes, $type, $title]) {
            $interruptions[] = Interruption::query()->create([
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'started_at' => $startedAt,
                'ends_at' => $startedAt->addMinutes($minutes),
                'ended_at' => $startedAt->addMinutes($minutes),
                'duration_minutes' => $minutes,
                'affected_plans' => 0,
            ]);
        }

        return $interruptions;
    }

    /**
     * @param  array<string, list<Goal>>  $goalsByMonth
     * @param  list<Interruption>  $interruptions
     */
    private function seedPlans(
        User $user,
        array $goalsByMonth,
        array $interruptions,
        CarbonImmutable $start,
        CarbonImmutable $today,
    ): void {
        $catalogue = [
            'English' => [
                ['English reading', 30],
                ['Listening practice', 45],
                ['Speaking club', 60],
                ['IELTS writing task', 45],
                ['Vocabulary review', 20],
            ],
            // Drawn from the portfolio, so a plan reads like real work rather
            // than a placeholder.
            'Projects' => $this->projectTasks(),
            'Personal' => [
                ['Reading', 40],
                ['Gym session', 60],
                ['Evening run', 30],
                ['Weekly review', 30],
                ['Family time', 60],
            ],
        ];

        $day = $start;

        while ($day->lessThanOrEqualTo($today)) {
            $goals = $goalsByMonth[$day->format('Y-m')] ?? [];

            if ($goals === []) {
                $day = $day->addDay();

                continue;
            }

            // Sundays are lighter; weekdays carry the real load.
            $count = $day->isSunday() ? mt_rand(2, 3) : mt_rand(4, 7);
            $hour = 9;

            for ($i = 0; $i < $count; $i++) {
                $goal = $goals[array_rand($goals)];
                [$title, $minutes] = $catalogue[$goal->title][array_rand($catalogue[$goal->title])];

                $startTime = $day->setTime($hour, [0, 15, 30][mt_rand(0, 2)]);
                $hour = min(21, $hour + mt_rand(1, 3));

                $this->createPlan($user, $goal, $interruptions, $title, $minutes, $startTime, $today);
            }

            $day = $day->addDay();
        }

        // Keep the interruption counters honest with the plans that reference them.
        foreach ($interruptions as $interruption) {
            $interruption->update([
                'affected_plans' => Plan::query()->where('interruption_id', $interruption->id)->count(),
            ]);
        }
    }

    /** @param  list<Interruption>  $interruptions */
    private function createPlan(
        User $user,
        Goal $goal,
        array $interruptions,
        string $title,
        int $minutes,
        CarbonImmutable $startTime,
        CarbonImmutable $today,
    ): void {
        $isFuture = $startTime->greaterThan(CarbonImmutable::now($user->timezone));

        $plan = Plan::query()->create([
            'user_id' => $user->id,
            'goal_id' => $goal->id,
            'title' => $title,
            'description' => null,
            'date' => $startTime->toDateString(),
            'start_time' => $startTime->format('H:i:s'),
            'planned_minutes' => $minutes,
            'priority' => $goal->priority,
            'status' => PlanStatus::Pending,
        ]);

        $this->event($plan, PlanEventType::Created, $startTime);

        if ($isFuture) {
            $plan->update(['next_reminder_at' => $startTime->utc()]);

            return;
        }

        $interruption = $this->interruptionCovering($interruptions, $startTime);

        if ($interruption !== null) {
            $this->settleAsInterrupted($plan, $interruption, $startTime);

            return;
        }

        $this->settleByBias($plan, $goal, $startTime, $minutes);
    }

    /** @param  list<Interruption>  $interruptions */
    private function interruptionCovering(array $interruptions, CarbonImmutable $at): ?Interruption
    {
        foreach ($interruptions as $interruption) {
            if ($at->betweenIncluded($interruption->started_at, $interruption->ends_at)) {
                return $interruption;
            }
        }

        return null;
    }

    private function settleAsInterrupted(Plan $plan, Interruption $interruption, CarbonImmutable $startTime): void
    {
        $plan->update([
            'status' => PlanStatus::Interrupted,
            'postpone_reason' => PostponeReason::Interruption,
            'interruption_id' => $interruption->id,
            'postpone_count' => 1,
            'reminder_count' => 1,
            'last_reminded_at' => $startTime->utc(),
        ]);

        $this->event($plan, PlanEventType::Interrupted, $startTime, [
            'interruption_type' => $interruption->type->value,
        ]);
    }

    private function settleByBias(Plan $plan, Goal $goal, CarbonImmutable $startTime, int $minutes): void
    {
        $band = match (true) {
            $startTime->hour < 13 => 'morning',
            $startTime->hour < 18 => 'afternoon',
            default => 'evening',
        };

        $chance = self::HOUR_BIAS[$band];

        // Personal is the goal that slips first when a day gets busy.
        if ($goal->title === 'Personal') {
            $chance -= 0.22;
        }

        $roll = mt_rand(0, 100) / 100;
        $postponements = 0;

        // A plan that was pushed once or twice and still got done is the most
        // interesting row in the history, so it is deliberately common.
        if ($roll < $chance && mt_rand(0, 100) < 35) {
            $postponements = mt_rand(1, 3);

            for ($i = 0; $i < $postponements; $i++) {
                $this->event($plan, PlanEventType::Postponed, $startTime->addMinutes(10 * ($i + 1)), [
                    'minutes' => [10, 10, 30][$i] ?? 30,
                ]);
            }
        }

        if ($roll < $chance) {
            $actual = (int) round($minutes * (mt_rand(75, 115) / 100));
            $completedAt = $startTime->addMinutes($minutes + 10 * $postponements);

            $plan->update([
                'status' => PlanStatus::Completed,
                'actual_minutes' => $actual,
                'completed_at' => $completedAt->utc(),
                'started_at' => $startTime->utc(),
                'postpone_count' => $postponements,
                'reminder_count' => 1 + $postponements,
                'last_reminded_at' => $startTime->utc(),
            ]);

            $this->event($plan, PlanEventType::Completed, $completedAt);

            return;
        }

        // The rest split between an explicit "no" and silence. Silence is rarer
        // but must exist: it is its own signal in the monthly report.
        if (mt_rand(0, 100) < 75) {
            $plan->update([
                'status' => PlanStatus::Failed,
                'fail_reason' => $this->pickFailReason(),
                'postpone_count' => $postponements,
                'reminder_count' => 2,
                'last_reminded_at' => $startTime->utc(),
            ]);

            $this->event($plan, PlanEventType::Failed, $startTime->addMinutes(20));

            return;
        }

        $plan->update([
            'status' => PlanStatus::NoResponse,
            'reminder_count' => 4,
            'last_reminded_at' => $startTime->addMinutes(30)->utc(),
        ]);

        $this->event($plan, PlanEventType::NoResponse, $startTime->addMinutes(40));
    }

    /**
     * Work items named after the real projects on the site, so the plan list
     * looks like the owner's actual week.
     *
     * @return list<array{0: string, 1: int}>
     */
    private function projectTasks(): array
    {
        $projects = Project::query()
            ->orderByDesc('featured')
            ->orderBy('sort_order')
            ->take(6)
            ->pluck('name');

        $verbs = [['Build', 120], ['Refactor', 90], ['Fix bugs in', 60], ['Write docs for', 45]];
        $tasks = [];

        foreach ($projects as $name) {
            $title = is_array($name) ? ($name['en'] ?? reset($name)) : $name;
            [$verb, $minutes] = $verbs[array_rand($verbs)];

            $tasks[] = ["{$verb} {$title}", $minutes];
        }

        return $tasks ?: [['Project work', 90]];
    }

    private function pickFailReason(): FailReason
    {
        return [
            FailReason::NoTime,
            FailReason::NoTime,
            FailReason::Overloaded,
            FailReason::Forgot,
            FailReason::NotImportant,
        ][mt_rand(0, 4)];
    }

    /** @param  array<string, mixed>  $metadata */
    private function event(Plan $plan, PlanEventType $type, CarbonImmutable $at, array $metadata = []): void
    {
        PlanEvent::query()->create([
            'plan_id' => $plan->id,
            'event_type' => $type,
            'from_time' => $plan->start_time,
            'to_time' => $type === PlanEventType::Postponed ? $at->format('H:i:s') : null,
            'metadata' => $metadata ?: null,
            'created_at' => $at->utc(),
        ]);
    }

    private function seedSiteNotifications(int $userId, CarbonImmutable $today): void
    {
        $samples = [
            [NotificationKind::SiteContact, 'New message — iqbolshoh.uz', "Name: John Doe\nEmail: john@example.com\n\nI need a website for my company.", NotificationStatus::Sent, 3],
            [NotificationKind::SiteOrder, 'New order — iqbolshoh.uz', "Name: Aziz Karimov\nService: Landing page\nPrice: 1 200 000+ UZS", NotificationStatus::Sent, 2],
            [NotificationKind::DailySummary, 'Daily summary', '6 plans · 5 completed · 1 failed · 83%', NotificationStatus::Sent, 1],
            [NotificationKind::MonthlyReport, 'Monthly report', 'Last month: 74.1% raw, 77.9% true completion.', NotificationStatus::Sent, 1],
            [NotificationKind::SiteContact, 'New message — iqbolshoh.uz', "Name: Test User\nEmail: test@example.com", NotificationStatus::Failed, 0],
        ];

        foreach ($samples as [$kind, $title, $body, $status, $daysAgo]) {
            $at = $today->subDays($daysAgo)->setTime(mt_rand(9, 20), mt_rand(0, 59));

            Notification::query()->create([
                'user_id' => $userId,
                'plan_id' => null,
                'kind' => $kind,
                'sequence' => 0,
                'title' => $title,
                'body' => $body,
                'channel' => 'telegram',
                'status' => $status,
                'chat_id' => config('services.telegram.chat_id') ?: null,
                'attempts' => $status === NotificationStatus::Failed ? 3 : 1,
                'error' => $status === NotificationStatus::Failed ? 'Telegram API: 401 Unauthorized' : null,
                'sent_at' => $status === NotificationStatus::Sent ? $at->utc() : null,
                'created_at' => $at->utc(),
                'updated_at' => $at->utc(),
            ]);
        }
    }
}
