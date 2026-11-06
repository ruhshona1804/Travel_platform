<?php

namespace App\Http\Controllers;

use App\Models\Attraction;
use App\Models\City;
use App\Models\Country;
use App\Models\Hotel;
use App\Models\Restaurant;
use App\Models\TravelGuide;

class SitemapController extends Controller
{
    public function __invoke()
    {
        $urls = collect([route('home'), route('countries.index'), route('attractions.index'), route('hotels.index'), route('restaurants.index'),
            route('uzbek-food'), route('airlines.index'), route('visa.index'), route('map'), route('planner'), route('budget'), route('currency.index'), route('guides.index')]);
        $urls = $urls
            ->merge(Country::published()->get()->map(fn ($m) => route('countries.show', $m)))
            ->merge(City::published()->get()->map(fn ($m) => route('cities.show', $m)))
            ->merge(Attraction::published()->get()->map(fn ($m) => route('attractions.show', $m)))
            ->merge(Hotel::published()->get()->map(fn ($m) => route('hotels.show', $m)))
            ->merge(Restaurant::published()->get()->map(fn ($m) => route('restaurants.show', $m)))
            ->merge(TravelGuide::published()->get()->map(fn ($m) => route('guides.show', $m)));
        $xml = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'
            .$urls->map(fn ($u) => '<url><loc>'.e($u).'</loc></url>')->implode('').'</urlset>';
        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
