<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->morphs('favoritable');
            $t->timestamps();
            $t->unique(['user_id', 'favoritable_type', 'favoritable_id']);
        });

        Schema::create('reviews', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->morphs('reviewable');
            $t->unsignedTinyInteger('rating');
            $t->text('body')->nullable();
            $t->boolean('is_approved')->default(true);
            $t->timestamps();
            $t->unique(['user_id', 'reviewable_type', 'reviewable_id']);
        });

        Schema::create('travel_plans', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $t->foreignId('country_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $t->string('title');
            $t->json('input');
            $t->string('generated_by', 20)->default('rules'); // rules, ai
            $t->date('start_date')->nullable();
            $t->date('end_date')->nullable();
            $t->decimal('total_usd', 12, 2)->default(0);
            $t->json('summary')->nullable();
            $t->json('tips')->nullable();
            $t->timestamps();
        });

        Schema::create('travel_plan_days', function (Blueprint $t) {
            $t->id();
            $t->foreignId('travel_plan_id')->constrained()->cascadeOnDelete();
            $t->unsignedSmallInteger('day_number');
            $t->string('title')->nullable();
            $t->decimal('cost_usd', 10, 2)->default(0);
            $t->timestamps();
        });

        Schema::create('travel_plan_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('travel_plan_day_id')->constrained()->cascadeOnDelete();
            $t->string('kind', 20); // attraction, restaurant, hotel, transport, note
            $t->nullableMorphs('itemable');
            $t->string('title');
            $t->string('note')->nullable();
            $t->decimal('cost_usd', 10, 2)->default(0);
            $t->boolean('cost_estimated')->default(true);
            $t->decimal('lat', 10, 7)->nullable();
            $t->decimal('lng', 10, 7)->nullable();
            $t->unsignedSmallInteger('position')->default(0);
            $t->timestamps();
        });

        Schema::create('budgets', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $t->foreignId('country_id')->nullable()->constrained()->nullOnDelete();
            $t->string('title');
            $t->unsignedSmallInteger('travelers')->default(1);
            $t->unsignedSmallInteger('days')->default(1);
            $t->string('currency', 3)->default('USD');
            $t->timestamps();
        });

        Schema::create('budget_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('budget_id')->constrained()->cascadeOnDelete();
            $t->string('category', 30);
            $t->decimal('amount_usd', 12, 2)->default(0);
            $t->boolean('is_estimate')->default(true);
            $t->timestamps();
        });

        Schema::create('travel_guides', function (Blueprint $t) {
            $t->id();
            $t->string('slug')->unique();
            $t->json('title');
            $t->json('excerpt')->nullable();
            $t->json('body')->nullable();
            $t->string('image')->nullable();
            $t->boolean('is_published')->default(true);
            $t->timestamps();
        });

        Schema::create('activity_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->string('action', 60);
            $t->string('subject')->nullable();
            $t->string('ip', 45)->nullable();
            $t->timestamps();
        });

        Schema::create('application_settings', function (Blueprint $t) {
            $t->string('key')->primary();
            $t->text('value')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['application_settings', 'activity_logs', 'travel_guides', 'budget_items', 'budgets', 'travel_plan_items', 'travel_plan_days', 'travel_plans', 'reviews', 'favorites'] as $n) Schema::dropIfExists($n);
    }
};
