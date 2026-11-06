<?php

namespace App\Http\Controllers;

use App\Models\Airport;
use App\Models\Attraction;
use App\Models\City;
use App\Models\Hotel;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function index()
    {
        return view('map');
    }

    /** Xarita markerlari (JSON). Koordinatasi yo'q yozuvlar o'tkazib yuboriladi. */
    public function data(Request $request)
    {
        $types = array_filter(explode(',', (string) $request->query('types', 'city,attraction,hotel,restaurant,uzbek,airport')));
        $q = trim((string) $request->query('q', ''));
        $out = collect();
        $has = fn ($m) => $m->whereNotNull('lat')->whereNotNull('lng');

        if (in_array('city', $types)) {
            $qq = $has(City::published()); if ($q) $qq->searchName($q);
            foreach ($qq->limit(300)->get() as $c) $out->push(['id' => 'city-'.$c->id, 'type' => 'city', 'name' => $c->tr('name'), 'lat' => (float) $c->lat, 'lng' => (float) $c->lng, 'url' => route('cities.show', $c), 'address' => null]);
        }
        foreach (['attraction' => Attraction::class, 'hotel' => Hotel::class, 'restaurant' => Restaurant::class] as $t => $m) {
            if (! in_array($t, $types) && ! ($t === 'restaurant' && in_array('uzbek', $types))) continue;
            $qq = $has($m::published()); if ($q) $qq->searchName($q);
            foreach ($qq->limit(500)->get() as $p) {
                $pt = $p->mapPoint();
                if (in_array($pt['type'], $types)) $out->push($pt);
            }
        }
        if (in_array('airport', $types)) {
            foreach (Airport::whereNotNull('lat')->get() as $a) $out->push(['id' => 'airport-'.$a->id, 'type' => 'airport', 'name' => $a->name, 'lat' => (float) $a->lat, 'lng' => (float) $a->lng, 'url' => null, 'address' => $a->iata]);
        }
        return response()->json($out->filter()->values());
    }
}
