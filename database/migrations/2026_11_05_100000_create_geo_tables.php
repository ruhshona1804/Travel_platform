<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $t) {
            $t->string('code', 3)->primary();
            $t->string('name');
            $t->string('symbol', 8);
            $t->decimal('rate_per_usd', 20, 8)->default(1);
            $t->boolean('is_active')->default(true);
            $t->boolean('is_manual')->default(false);
            $t->string('source', 30)->default('seed'); // seed, api, manual
            $t->timestamp('rate_updated_at')->nullable();
            $t->timestamps();
        });

        Schema::create('countries', function (Blueprint $t) {
            $t->id();
            $t->string('slug')->unique();
            $t->string('iso2', 2)->unique();
            $t->json('name');
            $t->json('capital')->nullable();
            $t->json('summary')->nullable();
            $t->json('climate')->nullable();
            $t->json('safety')->nullable();
            $t->json('customs')->nullable();
            $t->json('local_transport')->nullable();
            $t->string('continent', 20)->index();
            $t->string('languages')->nullable();
            $t->string('currency_code', 3)->nullable();
            $t->string('timezone')->nullable();
            $t->string('best_season')->nullable();
            $t->json('travel_types')->nullable();
            $t->json('local_apps')->nullable();
            $t->json('emergency_numbers')->nullable();
            $t->decimal('daily_budget_budget', 8, 2)->nullable();
            $t->decimal('daily_budget_comfort', 8, 2)->nullable();
            $t->decimal('daily_budget_luxury', 8, 2)->nullable();
            $t->decimal('lat', 10, 7)->nullable();
            $t->decimal('lng', 10, 7)->nullable();
            $t->string('image')->nullable();
            $t->string('cover_color', 7)->nullable();
            $t->unsignedInteger('popularity')->default(0);
            $t->boolean('is_published')->default(true);
            $t->boolean('is_demo')->default(false);
            $t->timestamps();
        });

        Schema::create('cities', function (Blueprint $t) {
            $t->id();
            $t->foreignId('country_id')->constrained()->cascadeOnDelete();
            $t->string('slug')->unique();
            $t->json('name');
            $t->json('summary')->nullable();
            $t->decimal('lat', 10, 7)->nullable();
            $t->decimal('lng', 10, 7)->nullable();
            $t->string('image')->nullable();
            $t->unsignedInteger('popularity')->default(0);
            $t->boolean('is_published')->default(true);
            $t->boolean('is_demo')->default(false);
            $t->timestamps();
        });

        Schema::create('airports', function (Blueprint $t) {
            $t->id();
            $t->foreignId('city_id')->constrained()->cascadeOnDelete();
            $t->string('iata', 3)->nullable();
            $t->string('name');
            $t->decimal('lat', 10, 7)->nullable();
            $t->decimal('lng', 10, 7)->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['airports', 'cities', 'countries', 'currencies'] as $n) Schema::dropIfExists($n);
    }
};
