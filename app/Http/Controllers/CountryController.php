<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\VisaRequirement;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function index(Request $request)
    {
        $q = Country::published()->with('visa');
        if ($s = trim((string) $request->query('q'))) {
            $q->where(fn ($w) => $w->searchName($s)->orWhereHas('cities', fn ($c) => $c->searchName($s)));
        }
        if ($c = $request->query('continent')) $q->where('continent', $c);
        if ($b = $request->query('budget')) {
            match ($b) {
                'low' => $q->where('daily_budget_comfort', '<=', 80),
                'mid' => $q->whereBetween('daily_budget_comfort', [80.01, 150]),
                'high' => $q->where('daily_budget_comfort', '>', 150),
                default => null,
            };
        }
        if ($t = $request->query('type')) $q->whereJsonContains('travel_types', $t);
        if ($se = $request->query('season')) $q->where('best_season', 'like', "%$se%");
        if ($v = $request->query('visa')) $q->whereHas('visa', fn ($w) => $w->where('status', $v));

        $countries = $q->orderByDesc('popularity')->paginate(12)->withQueryString();
        return view('countries.index', compact('countries'));
    }

    public function show(Country $country)
    {
        abort_unless($country->is_published, 404);
        $country->load([
            'visa',
            'cities' => fn ($q) => $q->published()->orderByDesc('popularity'),
            'cities.airports',
        ]);
        $attractions = $country->attractions()->published()->with('city')->orderByDesc('popularity')->limit(6)->get();
        $hotels = $country->hotels()->published()->with('city')->orderByDesc('rating')->limit(4)->get();
        $restaurants = $country->restaurants()->published()->with('city')->orderByDesc('popularity')->limit(4)->get();
        $uzbek = $country->restaurants()->published()->where('is_uzbek', true)->with('city')->limit(4)->get();
        $transport = \App\Models\TransportService::whereIn('city_id', $country->cities->pluck('id'))->with('city')->get();
        $points = collect()
            ->merge($attractions->map->mapPoint())->merge($hotels->map->mapPoint())->merge($restaurants->map->mapPoint())
            ->filter()->values();
        return view('countries.show', compact('country', 'attractions', 'hotels', 'restaurants', 'uzbek', 'transport', 'points'));
    }
}
