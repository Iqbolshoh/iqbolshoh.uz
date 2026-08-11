<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * iqbolshoh.uz sayt kontenti.
 *
 * Ko'p tilli maydonlar JSON ustunda saqlanadi: {"en":..,"uz":..,"ru":..,"tj":..}
 * `icon` — lucide-react ikonka nomi (masalan "Award"); frontend uni komponentga map qiladi.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Umumiy sozlamalar: ism, manzil, ijtimoiy tarmoqlar
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('value');
            $table->timestamps();
        });

        Schema::create('tech_stacks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon');
            $table->unsignedTinyInteger('level');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('stats', function (Blueprint $table) {
            $table->id();
            $table->string('icon');
            $table->string('value');
            $table->json('label');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->json('description');
            $table->string('image')->nullable();
            $table->json('tech');
            $table->string('live_demo')->nullable();
            $table->string('github')->nullable();
            $table->boolean('featured')->default(false);
            $table->string('category')->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('highlights', function (Blueprint $table) {
            $table->id();
            $table->string('icon');
            $table->json('text');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('journeys', function (Blueprint $table) {
            $table->id();
            $table->string('year');
            $table->json('title');
            $table->json('description');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // "Beyond coding" — dasturlashdan tashqari faoliyat
        Schema::create('beyonds', function (Blueprint $table) {
            $table->id();
            $table->string('icon');
            $table->json('title');
            $table->json('description');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('category')->index();
            $table->string('icon');
            $table->string('price')->nullable();
            $table->json('title');
            $table->json('description');
            $table->json('tech');
            $table->json('features');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('process_steps', function (Blueprint $table) {
            $table->id();
            $table->string('step');
            $table->json('title');
            $table->json('description');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });


        // Xizmat buyurtmalari (Services sahifasidagi modal forma)
        Schema::create('service_orders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->text('message')->nullable();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->string('service_name')->nullable();
            $table->string('service_price')->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        // Aloqa formasi xabarlari (eski send-message.php o'rniga)
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('subject')->nullable();
            $table->text('message');
            $table->string('ip', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach ([
            'contact_messages',
            'service_orders',
            'process_steps',
            'services',
            'beyonds',
            'journeys',
            'highlights',
            'projects',
            'stats',
            'tech_stacks',
            'site_settings',
        ] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
