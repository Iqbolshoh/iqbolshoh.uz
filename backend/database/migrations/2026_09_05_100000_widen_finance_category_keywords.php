<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Give the keyword list room to be useful.
 *
 * 255 characters was enough while a category was a broad bucket with a dozen
 * obvious words. It is not enough for a fine-grained catalogue in four
 * languages — the longest shipped list is already past three hundred — and it
 * has to hold whatever the bot learns on top of that for years.
 *
 * TEXT rather than a bigger VARCHAR: there is no length that is obviously
 * right, the column is never indexed and never sorted on, and running out
 * again means a failed write in the middle of a deploy.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('finance_categories', function (Blueprint $table) {
            $table->text('keywords')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('finance_categories', function (Blueprint $table) {
            $table->string('keywords', 255)->nullable()->change();
        });
    }
};
