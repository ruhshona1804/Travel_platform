<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function common(Blueprint $t): void
    {
        $t->id();
        $t->foreignId('country_id')->constrained()->cascadeOnDelete();
        $t->foreignId('city_id')->constrained()->cascadeOnDelete();
        $t->string('slug')->unique();
        $t->json('name');
        $t->json('description')->nullable();
        $t->string('address')->nullable();
        $t->decimal('lat', 10, 7)->nullable();
        $t->decimal('lng', 10, 7)->nullable();
        $t->decimal('rating', 3, 2)->nullable();
        $t->string('website')->nullable();
        $t->string('image')->nullable();
        $t->json('gallery')->nullable();
        $t->unsignedInteger('popularity')->default(0);
        $t->boolean('is_published')->default(true);
        $t->boolean('is_demo')->default(false);
        $t->boolean('price_verified')->default(false);
    }

    public function up(): void
    {
        Schema::create('attractions', function (Blueprint $t) {
            $this->common($t);
            $t->string('category', 30)->index();
            $t->string('opening_hours')->nullable();
            $t->decimal('entrance_usd', 10, 2)->nullable();
            $t->unsignedSmallInteger('duration_min')->nullable();
            $t->string('transport_info')->nullable();
            $t->timestamps();
        });

        Schema::create('hotels', function (Blueprint $t) {
            $this->common($t);
            $t->string('type', 30)->index(); // budget, luxury, hostel, apartment, family, guesthouse, resort
            $t->decimal('price_usd', 10, 2)->nullable();
            $t->unsignedTinyInteger('stars')->nullable();
            $t->json('facilities')->nullable();
            $t->string('checkin', 20)->nullable();
            $t->string('checkout', 20)->nullable();
            $t->decimal('airport_km', 6, 1)->nullable();
            $t->string('booking_url')->nullable();
            $t->timestamps();
        });

        Schema::create('restaurants', function (Blueprint $t) {
            $this->common($t);
            $t->string('category', 30)->index(); // uzbek, halal, local, international, fastfood, cafe, budget, premium
            $t->string('cuisine')->nullable();
            $t->boolean('is_uzbek')->default(false)->index();
            $t->boolean('is_halal')->default(false)->index();
            $t->decimal('avg_meal_usd', 10, 2)->nullable();
            $t->string('opening_hours')->nullable();
            $t->string('phone')->nullable();
            $t->timestamps();
        });

        Schema::create('airlines', function (Blueprint $t) {
            $t->id();
            $t->string('slug')->unique();
            $t->string('code', 3)->nullable();
            $t->string('name');
            $t->json('description')->nullable();
            $t->string('website')->nullable();
            $t->string('baggage_url')->nullable();
            $t->string('logo')->nullable();
            $t->string('hub')->nullable();
            $t->decimal('rating', 3, 2)->nullable();
            $t->boolean('is_published')->default(true);
            $t->boolean('is_demo')->default(false);
            $t->timestamps();
        });

        Schema::create('flights', function (Blueprint $t) {
            $t->id();
            $t->foreignId('airline_id')->constrained()->cascadeOnDelete();
            $t->string('from_city');
            $t->foreignId('to_city_id')->constrained('cities')->cascadeOnDelete();
            $t->time('departure_time')->nullable();
            $t->time('arrival_time')->nullable();
            $t->unsignedSmallInteger('duration_min')->nullable();
            $t->boolean('is_direct')->default(true);
            $t->string('transit')->nullable();
            $t->unsignedSmallInteger('baggage_kg')->nullable();
            $t->decimal('est_price_usd', 10, 2)->nullable();
            $t->boolean('is_sample')->default(true);
            $t->timestamps();
        });

        Schema::create('visa_requirements', function (Blueprint $t) {
            $t->id();
            $t->foreignId('country_id')->unique()->constrained()->cascadeOnDelete();
            $t->string('status', 20)->default('check_official'); // visa_free, visa_required, on_arrival, e_visa, check_official
            $t->unsignedSmallInteger('max_stay_days')->nullable();
            $t->string('passport_validity')->nullable();
            $t->json('documents')->nullable();
            $t->json('notes')->nullable();
            $t->boolean('insurance_required')->nullable();
            $t->string('official_url')->nullable();
            $t->string('source_url')->nullable();
            $t->date('last_verified_at')->nullable();
            $t->string('verification_status', 20)->default('unverified'); // verified, unverified, demo
            $t->timestamps();
        });

        Schema::create('transport_services', function (Blueprint $t) {
            $t->id();
            $t->foreignId('city_id')->constrained()->cascadeOnDelete();
            $t->string('type', 30)->index(); // taxi, airport_transfer, metro, bus, train, tram, car_rental, intercity
            $t->string('name');
            $t->json('description')->nullable();
            $t->string('website')->nullable();
            $t->decimal('start_fare_usd', 8, 2)->nullable();
            $t->decimal('per_km_usd', 8, 2)->nullable();
            $t->boolean('price_verified')->default(false);
            $t->boolean('is_demo')->default(false);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['transport_services', 'visa_requirements', 'flights', 'airlines', 'restaurants', 'hotels', 'attractions'] as $n) Schema::dropIfExists($n);
    }
};
