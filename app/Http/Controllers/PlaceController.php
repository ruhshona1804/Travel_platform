<?php

namespace App\Http\Controllers;

use App\Models\Attraction;
use App\Models\City;
use App\Models\Country;
use App\Models\Hotel;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class PlaceController extends Controller
{
    private const KINDS = [
        'attractions' => [Attraction::class, 'category', 'entrance_usd', Attraction::CATEGORIES],
        'hotels' => [Hotel::class, 'type', 'price_usd', Hotel::TYPES],
        'restaurants' => [Restaurant::class, 'category', 'avg_meal_usd', Restaurant::CATEGORIES],
    ];

    public function index(Request $request, string $kind)
    {
        [$model, $catCol, $priceCol, $cats] = self::KINDS[$kind];
        $q = $model::published()->with('city.country');
        if ($s = trim((string) $request->query('q'))) $q->searchName($s);
        if ($c = $request->query('country')) $q->whereHas('country', fn ($w) => $w->where('slug', $c));
        if ($c = $request->query('city')) $q->whereHas('city', fn ($w) => $w->where('slug', $c));
        if (($cat = $request->query('category')) && in_array($cat, $cats, true)) {
            if ($kind === 'restaurants' && $cat === 'uzbek') $q->where('is_uzbek', true);
            elseif ($kind === 'restaurants' && $cat === 'halal') $q->where('is_halal', true);
            else $q->where($catCol, $cat);
        }
        if (($max = $request->query('max_price')) !== null && $max !== '' && is_numeric($max)) $q->where($priceCol, '<=', $max);
        if (($min = $request->query('min_rating')) && is_numeric($min)) $q->where('rating', '>=', $min);

        match ($request->query('sort')) {
            'price' => $q->orderByRaw("$priceCol is null, $priceCol asc"),
            'rating' => $q->orderByDesc('rating'),
            default => $q->orderByDesc('popularity'),
        };

        $items = $q->paginate(12)->withQueryString();
        return view("places.index", [
            'items' => $items, 'kind' => $kind, 'categories' => $cats, 'priceCol' => $priceCol,
            'countries' => Country::published()->orderBy('slug')->get(), 'cities' => City::published()->orderBy('slug')->get(),
            'points' => collect($items->items())->map->mapPoint()->filter()->values(),
        ]);
    }

    public function show(string $place, string $kind)
    {
        [$model, $catCol, $priceCol] = self::KINDS[$kind];
        $item = $model::published()->with(['city.country.visa', 'city.airports'])->where('slug', $place)->firstOrFail();
        $item->load(['reviews.user']);

        $nearby = collect();
        if ($item->hasCoords()) {
            foreach (self::KINDS as $k => [$m]) {
                if ($m === $model) continue;
                foreach ($m::published()->where('city_id', $item->city_id)->get() as $o) {
                    $d = $o->distanceTo((float) $item->lat, (float) $item->lng);
                    if ($d !== null && $d <= 5) $nearby->push(['model' => $o, 'kind' => $k, 'km' => $d]);
                }
            }
            $nearby = $nearby->sortBy('km')->take(8)->values();
        }
        $airport = $item->city->airports->first(fn ($a) => $a->lat);
        $airportKm = $airport && $item->hasCoords() ? \App\Support\Geo::distanceKm((float) $item->lat, (float) $item->lng, (float) $airport->lat, (float) $airport->lng) : null;
        $transport = $item->city->transportServices()->get();
        $similar = $model::published()->where('city_id', $item->city_id)->where('id', '!=', $item->id)->orderByDesc('rating')->limit(3)->get();

        return view('places.show', compact('item', 'kind', 'nearby', 'airport', 'airportKm', 'transport', 'similar', 'catCol', 'priceCol'));
    }

    public function uzbekFood(Request $request)
    {
        $q = Restaurant::published()->where('is_uzbek', true)->with('city.country');
        if ($c = $request->query('country')) $q->whereHas('country', fn ($w) => $w->where('slug', $c));
        $items = $q->orderByDesc('rating')->get();
        $countries = Country::published()->whereHas('restaurants', fn ($w) => $w->where('is_uzbek', true))->get();
        return view('places.uzbek-food', ['items' => $items, 'countries' => $countries, 'points' => $items->map->mapPoint()->filter()->values()]);
    }
}
