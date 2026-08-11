<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The blog section was removed from the site, so its table goes with it.
 *
 * `down()` recreates the structure but not the rows: the existing posts were
 * exported to a separate backup before the table was dropped.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('blog_posts');
    }

    public function down(): void
    {
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->json('title');
            $table->json('excerpt');
            $table->string('image')->nullable();
            $table->date('date');
            $table->json('tags');
            $table->boolean('featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }
};
