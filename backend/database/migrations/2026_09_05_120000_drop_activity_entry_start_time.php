<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Drop a column nothing ever wrote to.
 *
 * `activity_entries.started_at_time` was created alongside the table and
 * contradicted its own design note in the same file: an entry carries a
 * duration and never a clock reading, because these are written down after the
 * fact. Nothing set it, nothing read it, and a column the code does not fill
 * is one the next reader has to reverse-engineer a meaning for.
 *
 * Guarded on both sides so it is safe on an install that never had the column
 * — the create migration no longer declares it, so a fresh database reaches
 * this point with nothing to drop.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('activity_entries', 'started_at_time')) {
            return;
        }

        Schema::table('activity_entries', function (Blueprint $table) {
            $table->dropColumn('started_at_time');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('activity_entries', 'started_at_time')) {
            return;
        }

        Schema::table('activity_entries', function (Blueprint $table) {
            $table->time('started_at_time')->nullable()->after('date');
        });
    }
};
