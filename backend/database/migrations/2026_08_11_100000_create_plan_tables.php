<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The Plan system: monthly goals, the daily plans under them, the event trail
 * every plan leaves behind, interruptions, the outgoing notification log and
 * the monthly forecast.
 *
 * Two decisions are worth stating here because the rest of the system leans on
 * them:
 *
 * 1. A plan carries its own reminder state (`next_reminder_at`,
 *    `reminder_count`). The scheduler then selects due plans with one indexed
 *    query per minute instead of walking a separate reminder queue.
 *
 * 2. "Not done" is never one thing. `status` says what happened, and
 *    `postpone_reason` / `fail_reason` / `interruption_id` say why — which is
 *    what lets the monthly report separate a meeting that ran long from a plan
 *    the owner simply skipped.
 *
 * Times: timestamps are UTC, but `date` and `start_time` are wall-clock in the
 * user's own timezone. Storing 09:00 as an absolute instant would move the plan
 * whenever the server's zone changed.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Always the first day of the month the goal belongs to, so a month
            // is a range query rather than string matching on "2026-08".
            $table->date('month');

            $table->string('title');
            $table->text('description')->nullable();

            // Free text on purpose: "30 hours", "8 lessons", "every weekday".
            $table->string('target')->nullable();

            $table->string('priority', 16)->default('medium');
            $table->string('status', 16)->default('active');
            $table->string('color', 7)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['user_id', 'month']);
            $table->index(['user_id', 'status']);
        });

        Schema::create('interruptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // meeting | travel | guest | class | work | rest | emergency | other
            $table->string('type', 24);
            $table->string('title')->nullable();

            $table->timestamp('started_at');

            // When the owner said they would be free again.
            $table->timestamp('ends_at')->nullable();

            // When they actually were — set by the "I'm free" reply or by the
            // scheduler once `ends_at` passes.
            $table->timestamp('ended_at')->nullable();

            $table->unsignedInteger('duration_minutes')->nullable();
            $table->unsignedInteger('affected_plans')->default(0);
            $table->timestamps();

            // The reminder dispatcher asks "is anything active right now?" every
            // minute, and this is the index that answers it.
            $table->index(['user_id', 'started_at', 'ended_at']);
        });

        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // A plan can stand on its own; not everything belongs to a goal.
            $table->foreignId('goal_id')->nullable()->constrained()->nullOnDelete();

            $table->string('title');
            $table->text('description')->nullable();

            $table->date('date');
            $table->time('start_time');
            $table->unsignedSmallInteger('planned_minutes')->default(30);
            $table->unsignedSmallInteger('actual_minutes')->nullable();

            // pending | in_progress | completed | failed | postponed |
            // interrupted | no_response | cancelled
            $table->string('status', 16)->default('pending');
            $table->string('priority', 16)->default('medium');

            // self | interruption — the distinction the whole forecast rests on.
            $table->string('postpone_reason', 24)->nullable();
            $table->string('fail_reason', 32)->nullable();

            $table->foreignId('interruption_id')->nullable()->constrained()->nullOnDelete();

            $table->unsignedTinyInteger('reminder_count')->default(0);
            $table->timestamp('next_reminder_at')->nullable();
            $table->timestamp('last_reminded_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            // How many times this plan has been pushed, kept denormalised
            // because the forecast segments on it on every run.
            $table->unsignedTinyInteger('postpone_count')->default(0);

            $table->timestamps();

            $table->index(['user_id', 'date']);
            $table->index(['user_id', 'status']);
            $table->index(['goal_id', 'status']);

            // The one query the scheduler runs every minute.
            $table->index(['status', 'next_reminder_at']);
        });

        Schema::create('plan_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained()->cascadeOnDelete();

            // created | reminded | started | postponed | completed | failed |
            // no_response | interrupted | rescheduled | cancelled
            $table->string('event_type', 24);

            // Set on a move, so the trail shows 09:00 → 09:30 rather than just
            // "postponed".
            $table->time('from_time')->nullable();
            $table->time('to_time')->nullable();

            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['plan_id', 'created_at']);
            $table->index(['event_type', 'created_at']);
        });

        Schema::create('telegram_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Telegram ids exceed a signed 32-bit int, so this is a bigint.
            $table->unsignedBigInteger('telegram_id')->unique();

            $table->string('username')->nullable();
            $table->string('first_name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('linked_at')->nullable();
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->nullable()->constrained()->cascadeOnDelete();

            // reminder | daily_summary | weekly_summary | monthly_report |
            // forecast | site_contact | site_order | interruption
            $table->string('kind', 24);

            // Which reminder in the series this is. Together with plan_id and
            // kind it makes a resend impossible: a job that runs twice fails the
            // unique index on its second insert instead of sending twice.
            $table->unsignedTinyInteger('sequence')->default(0);

            $table->string('title');
            $table->text('body')->nullable();
            $table->string('channel', 16)->default('telegram');

            // pending | sent | failed
            $table->string('status', 16)->default('pending');

            $table->unsignedBigInteger('chat_id')->nullable();
            $table->unsignedBigInteger('message_id')->nullable();
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->text('error')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->unique(['plan_id', 'kind', 'sequence']);
            $table->index(['user_id', 'status']);
            $table->index(['kind', 'created_at']);
        });

        Schema::create('plan_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            $table->unsignedSmallInteger('reminder_repeat_minutes')->default(10);
            $table->unsignedTinyInteger('max_reminders')->default(4);
            $table->unsignedSmallInteger('max_reminder_window_minutes')->default(60);

            $table->boolean('daily_summary')->default(true);
            $table->boolean('weekly_summary')->default(true);
            $table->boolean('monthly_report')->default(true);
            $table->boolean('forecast')->default(true);

            $table->time('daily_summary_time')->default('21:00:00');
            $table->boolean('quiet_mode')->default(false);
            $table->timestamps();
        });

        Schema::create('forecast_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // The month being forecast, not the month it was built from.
            $table->date('month');

            $table->unsignedInteger('source_plans')->default(0);
            $table->unsignedInteger('source_completed')->default(0);
            $table->decimal('raw_rate', 5, 2)->default(0);
            $table->decimal('true_rate', 5, 2)->default(0);

            // low | medium | high — derived from the sample size, so a month of
            // six plans never presents itself as a confident prediction.
            $table->string('confidence', 8)->default('low');

            $table->json('projection')->nullable();
            $table->json('segments')->nullable();
            $table->json('recommendations')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forecast_reports');
        Schema::dropIfExists('plan_settings');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('telegram_accounts');
        Schema::dropIfExists('plan_events');
        Schema::dropIfExists('plans');
        Schema::dropIfExists('interruptions');
        Schema::dropIfExists('goals');
    }
};
