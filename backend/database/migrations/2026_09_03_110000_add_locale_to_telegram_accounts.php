<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The language the bot answers a chat in.
 *
 * It lives on the Telegram account rather than the user, because the panel and
 * the bot are two different audiences: the owner reads the admin panel in
 * English and may well want the bot in Uzbek, and one column serving both
 * would force a choice between them.
 *
 * Null means "not chosen yet" — the bot then follows the client's own language
 * on first contact and asks nothing.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('telegram_accounts', function (Blueprint $table) {
            $table->string('locale', 5)->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('telegram_accounts', function (Blueprint $table) {
            $table->dropColumn('locale');
        });
    }
};
