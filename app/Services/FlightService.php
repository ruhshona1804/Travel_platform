<?php

namespace App\Services;

use App\Models\Flight;

/**
 * Parvoz ma'lumotlari. Jonli provayder ulanmagan bo'lsa, faqat bazadagi
 * namunaviy (is_sample) yozuvlar qaytariladi va shunday belgilanadi.
 */
class FlightService
{
    public function liveProviderConnected(): bool
    {
        return config('travel.flights.provider') !== 'none';
    }

    public function forCity(int $cityId, ?string $sort = 'price')
    {
        $q = Flight::with('airline', 'toCity')->where('to_city_id', $cityId);
        match ($sort) {
            'duration' => $q->orderBy('duration_min'),
            'departure' => $q->orderBy('departure_time'),
            default => $q->orderByRaw('est_price_usd is null, est_price_usd'),
        };
        return $q->get();
    }
}
