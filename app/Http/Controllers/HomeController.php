<?php

namespace App\Http\Controllers;

use App\Models\Attraction;
use App\Models\City;
use App\Models\Country;
use App\Models\Hotel;
use App\Models\Restaurant;
use App\Models\TravelGuide;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $countries = Country::published()->orderByDesc('popularity')->get();
        $cities = City::published()->with('country')->orderByDesc('popularity')->get();
        return view('home', [
            'countries' => $countries->take(8),
            'cities' => $cities->take(6),
            'trending' => $cities->sortByDesc('popularity')->take(4),
            'budgetCountries' => $countries->whereNotNull('daily_budget_budget')->sortBy('daily_budget_budget')->take(4),
            'hotels' => Hotel::published()->with('city')->orderByDesc('rating')->limit(4)->get(),
            'restaurants' => Restaurant::published()->with('city')->orderByDesc('popularity')->limit(4)->get(),
            'attractions' => Attraction::published()->with('city')->orderByDesc('popularity')->limit(4)->get(),
            'guides' => TravelGuide::published()->latest()->limit(3)->get(),
            'allCountries' => $countries,
            'allCities' => $cities,
            'mapPoints' => $cities->filter(fn ($c) => $c->lat)->map(fn ($c) => [
                'id' => 'city-'.$c->id, 'type' => 'city', 'name' => $c->tr('name'), 'lat' => (float) $c->lat, 'lng' => (float) $c->lng,
                'url' => route('cities.show', $c), 'address' => $c->country->tr('name'),
            ])->values(),
        ]);
    }

    private function find(string $q): array
    {
        $take = fn ($query) => $query->searchName($q)->limit(6)->get();
        return [
            'countries' => $take(Country::published()),
            'cities' => $take(City::published()->with('country')),
            'attractions' => $take(Attraction::published()->with('city')),
            'hotels' => $take(Hotel::published()->with('city')),
            'restaurants' => $take(Restaurant::published()->with('city')),
        ];
    }

    public function search(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $results = mb_strlen($q) >= 2 ? $this->find($q) : null;
        return view('search', compact('q', 'results'));
    }

    public function suggest(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        if (mb_strlen($q) < 2) return response()->json([]);
        $r = $this->find($q);
        $out = [];
        foreach ($r['countries'] as $m) $out[] = ['label' => $m->flag().' '.$m->tr('name'), 'sub' => __('ui.country'), 'url' => route('countries.show', $m)];
        foreach ($r['cities'] as $m) $out[] = ['label' => $m->tr('name'), 'sub' => $m->country->tr('name'), 'url' => route('cities.show', $m)];
        foreach ($r['attractions'] as $m) $out[] = ['label' => $m->tr('name'), 'sub' => __('ui.attraction'), 'url' => route('attractions.show', $m)];
        foreach ($r['hotels'] as $m) $out[] = ['label' => $m->tr('name'), 'sub' => __('ui.hotel'), 'url' => route('hotels.show', $m)];
        foreach ($r['restaurants'] as $m) $out[] = ['label' => $m->tr('name'), 'sub' => __('ui.restaurant'), 'url' => route('restaurants.show', $m)];
        return response()->json(array_slice($out, 0, 10));
    }
}
