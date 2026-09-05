<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The Finance system: what the money was spent on, what came in, and the
 * limits the owner set for themselves.
 *
 * Three decisions the rest of the module leans on:
 *
 * 1. Money is a whole number of so'm in an unsigned bigint. No floats, no
 *    minor units — the som has no subunit in practice, and 2 500 000 000 fits
 *    with room to spare. Anything that needs another currency converts for
 *    display only and never writes a second column.
 *
 * 2. Income and expense share one `transactions` table with a `kind` column.
 *    The balance for a month is then one grouped query rather than two
 *    queries and a subtraction, and a category can never be half-migrated
 *    between the two shapes.
 *
 * 3. `date` and `time` are wall-clock in the owner's timezone, exactly as a
 *    plan's are. A coffee bought at 23:50 belongs to that day's total even
 *    when the server is already on tomorrow in UTC.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finance_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // expense | income
            $table->string('kind', 16);

            // Set on the categories the installer seeds, null on anything the
            // owner adds later. The bot translates a seeded category through
            // `finance.categories.<key>`; a hand-made one keeps `name` as typed
            // in every language, which is the honest thing to show.
            $table->string('key', 40)->nullable();

            $table->string('name', 60);
            $table->string('icon', 16)->nullable();
            $table->string('color', 7)->nullable();

            // Words the bot matches free text against, comma separated and
            // lower case: "ovqat,eda,food,хӯрок". Filled for seeded categories,
            // the owner can extend it, and the bot appends to it whenever a
            // guess is corrected.
            //
            // TEXT, not a VARCHAR: this started at 255 and the fine-grained
            // catalogue blew through it on the first sync. There is no length
            // that is obviously right, the column is never indexed or sorted
            // on, and running out again means a failed write in the middle of
            // a deploy. Installs made before that are widened by
            // 2026_09_05_100000_widen_finance_category_keywords, which is a
            // no-op here.
            $table->text('keywords')->nullable();

            // Per-category ceiling for one month, in so'm. Null = no ceiling.
            $table->unsignedBigInteger('monthly_limit')->nullable();

            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['user_id', 'kind', 'is_active']);
            $table->unique(['user_id', 'kind', 'name']);
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // A deleted category must not take its history with it: the money
            // still left the pocket. The row survives as "uncategorised".
            $table->foreignId('category_id')->nullable()
                ->constrained('finance_categories')->nullOnDelete();

            // expense | income — denormalised from the category on purpose, so
            // an uncategorised row still knows which way the money went.
            $table->string('kind', 16);

            $table->unsignedBigInteger('amount');

            $table->date('date');
            $table->time('time')->nullable();

            $table->string('note', 255)->nullable();

            // cash | card | transfer | other
            $table->string('method', 16)->default('cash');

            // web | telegram — tells the owner where a row came from, and lets
            // the bot offer "undo the last thing I added".
            $table->string('source', 16)->default('web');

            $table->timestamps();

            $table->index(['user_id', 'date']);
            $table->index(['user_id', 'kind', 'date']);
            $table->index(['user_id', 'source', 'created_at']);
        });

        Schema::create('finance_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            // Whole month ceiling across every expense category, in so'm.
            $table->unsignedBigInteger('monthly_budget')->nullable();

            // Warn once a category (or the month) crosses this share of its
            // ceiling, so the message arrives while it can still change
            // something rather than after the money is gone.
            $table->unsignedTinyInteger('warn_at_percent')->default(80);

            // The evening nudge: "what did today cost?". Sent by the bot at
            // `prompt_time` in the owner's timezone, and only on a day that has
            // no expense logged yet — a day already accounted for needs no
            // reminder.
            $table->boolean('daily_prompt')->default(true);
            $table->time('prompt_time')->default('21:00:00');

            $table->boolean('weekly_report')->default(true);
            $table->boolean('monthly_report')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_settings');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('finance_categories');
    }
};
