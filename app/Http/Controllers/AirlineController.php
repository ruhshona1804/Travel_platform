<?php

namespace App\Http\Controllers;

use App\Models\Airline;
use App\Models\City;
use App\Services\FlightService;
use Illuminate\Http\Request;

class AirlineController extends Controller
{
    public function index()
    {
        return view('airlines.index', ['airlines' => Airline::where('is_published', true)->orderByDesc('rating')->get()]);
    }

    public function show(Airline $airline)
    {
        abort_unless($airline->is_published, 404);
        $airline->load('flights.toCity.country');
        return view('airlines.show', compact('airline'));
    }

    public function compare(Request $request, FlightService $flights)
    {
        $cities = City::published()->with('country')->orderBy('slug')->get();
        $cityId = (int) $request->query('city', 0);
        $sort = in_array($request->query('sort'), ['price', 'duration', 'departure'], true) ? $request->query('sort') : 'price';
        $city = $cityId ? $cities->firstWhere('id', $cityId) : null;
        $list = $city ? $flights->forCity($city->id, $sort) : collect();
        if ($request->boolean('direct')) $list = $list->where('is_direct', true);
        return view('airlines.compare', ['cities' => $cities, 'city' => $city, 'flights' => $list, 'sort' => $sort, 'live' => $flights->liveProviderConnected()]);
    }
}
