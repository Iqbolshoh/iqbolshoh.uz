<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Where the day went.
 *
 * The third thing this system keeps track of, after plans and money, and
 * deliberately its own pair of tables rather than a column bolted onto either.
 * A plan is something intended and then settled; an interruption is a period
 * of being unreachable. Neither can answer "I slept eight hours" — sleep is
 * never planned and never interrupts anything — and stretching one of them to
 * cover it would have made both worse.
 *
 * Three decisions the rest of the module leans on:
 *
 * 1. Duration is whole minutes in an unsigned integer, never a start/end pair.
 *    What gets logged is "three hours of work", usually hours after the fact,
 *    and a pair of timestamps would force an invented start time and then
 *    invite questions about overlaps that the data cannot honestly answer.
 *
 * 2. `date` is wall-clock in the owner's timezone, exactly as a plan's and a
 *    transaction's are. Sleep logged at 07:00 belongs to the day it is written
 *    down on, and UTC is still on yesterday.
 *
 * 3. An entry keeps its own `minutes` even when its category is deleted. The
 *    hours were still spent; only the label goes.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Set on the categories the installer seeds, null on anything the
            // owner adds. A seeded one is shown through
            // `activity.categories.<key>`; a hand-made one keeps its name in
            // every language, which is the honest thing to show.
            $table->string('key', 40)->nullable();

            $table->string('name', 60);
            $table->string('icon', 16)->nullable();
            $table->string('color', 7)->nullable();

            // Words a free-text line is matched against: "uyqu,sleep,сон,хоб".
            $table->text('keywords')->nullable();

            // What a good day looks like for this activity, in minutes. Null
            // means "no opinion" — most activities do not want a target, and a
            // target invented for them would only produce noise.
            $table->unsignedSmallInteger('daily_target_minutes')->nullable();

            // Whether more is better (sport, study) or less is (phone, idle).
            // The report needs it to know which way a bar is going.
            $table->boolean('is_good')->default(true);

            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['user_id', 'is_active']);
            $table->unique(['user_id', 'name']);
        });

        Schema::create('activity_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->foreignId('category_id')->nullable()
                ->constrained('activity_categories')->nullOnDelete();

            $table->unsignedInteger('minutes');

            $table->date('date');

            // No start time, on purpose — see decision 1 above. It was here for
            // one afternoon, nothing ever wrote to it, and a column the code
            // does not fill is a column the next reader has to work out the
            // meaning of. Dropped by
            // 2026_09_05_120000_drop_activity_entry_start_time for the install
            // that already had it.

            $table->string('note', 255)->nullable();

            // web | telegram | status — "status" is an entry the bot wrote by
            // itself when an interruption ended, and it is labelled so the
            // owner can tell it from something they typed.
            $table->string('source', 16)->default('web');

            // The interruption this was derived from, when it was. Nulled
            // rather than cascaded: the hours stay even if the interruption
            // record is tidied away, and the foreign key exists only to stop
            // the same interruption being logged twice.
            $table->foreignId('interruption_id')->nullable()
                ->constrained()->nullOnDelete();

            $table->timestamps();

            $table->index(['user_id', 'date']);
            $table->index(['user_id', 'source', 'created_at']);
            $table->unique('interruption_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_entries');
        Schema::dropIfExists('activity_categories');
    }
};
