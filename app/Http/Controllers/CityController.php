<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Services\FlightService;

class CityController extends Controller
{
    public function show(City $city, FlightService $flights)
    {
        abort_unless($city->is_published, 404);
        $city->load('country.visa', 'airports');
        $attractions = $city->attractions()->published()->orderByDesc('popularity')->get();
        $hotels = $city->hotels()->published()->orderByDesc('rating')->get();
        $restaurants = $city->restaurants()->published()->orderByDesc('popularity')->get();
        $transport = $city->transportServices()->get();
        $points = collect()->merge($attractions->map->mapPoint())->merge($hotels->map->mapPoint())->merge($restaurants->map->mapPoint())
            ->merge($city->airports->filter(fn ($a) => $a->lat)->map(fn ($a) => ['id' => 'airport-'.$a->id, 'type' => 'airport', 'name' => $a->name, 'lat' => (float) $a->lat, 'lng' => (float) $a->lng, 'url' => null, 'address' => $a->iata]))
            ->filter()->values();
        $flightList = $flights->forCity($city->id);
        return view('cities.show', compact('city', 'attractions', 'hotels', 'restaurants', 'transport', 'points', 'flightList'));
    }
}
