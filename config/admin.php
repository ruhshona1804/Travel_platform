<?php

use App\Models\Airline;
use App\Models\Airport;
use App\Models\Attraction;
use App\Models\City;
use App\Models\Country;
use App\Models\Flight;
use App\Models\Hotel;
use App\Models\Restaurant;
use App\Models\TransportService;
use App\Models\TravelGuide;
use App\Models\VisaRequirement;

/*
 | Admin paneldagi umumiy CRUD resurslari.
 | Maydon turlari: text, textarea, number, url, date, time, select, bool, image, images,
 |                 tr (tarjimali qisqa matn), trtext (tarjimali uzun matn), csv, kv, multi, relation
 */
$latlng = [
    ['name' => 'lat', 'type' => 'number', 'step' => '0.0000001', 'rules' => 'nullable|numeric|between:-90,90'],
    ['name' => 'lng', 'type' => 'number', 'step' => '0.0000001', 'rules' => 'nullable|numeric|between:-180,180'],
];
$flags = [
    ['name' => 'is_published', 'type' => 'bool', 'default' => true],
    ['name' => 'is_demo', 'type' => 'bool'],
];
$placeBase = fn (string $table) => [
    ['name' => 'slug', 'type' => 'text', 'rules' => "required|alpha_dash|max:120", 'unique' => $table],
    ['name' => 'country_id', 'type' => 'relation', 'model' => Country::class, 'rules' => 'required|exists:countries,id'],
    ['name' => 'city_id', 'type' => 'relation', 'model' => City::class, 'rules' => 'required|exists:cities,id'],
    ['name' => 'name', 'type' => 'tr', 'required' => true],
    ['name' => 'description', 'type' => 'trtext'],
    ['name' => 'address', 'type' => 'text', 'rules' => 'nullable|string|max:255'],
    ...$latlng,
    ['name' => 'rating', 'type' => 'number', 'step' => '0.01', 'rules' => 'nullable|numeric|between:0,5'],
    ['name' => 'website', 'type' => 'url', 'rules' => 'nullable|url|max:255'],
    ['name' => 'image', 'type' => 'image'],
    ['name' => 'gallery', 'type' => 'images'],
    ['name' => 'popularity', 'type' => 'number', 'rules' => 'nullable|integer|min:0', 'default' => 0],
    ['name' => 'price_verified', 'type' => 'bool'],
    ...$flags,
];

return [
    'countries' => [
        'model' => Country::class, 'columns' => ['name:tr', 'iso2', 'continent', 'popularity'], 'order' => 'popularity',
        'fields' => [
            ['name' => 'slug', 'type' => 'text', 'rules' => 'required|alpha_dash|max:100', 'unique' => 'countries'],
            ['name' => 'iso2', 'type' => 'text', 'rules' => 'required|alpha|size:2', 'unique' => 'countries'],
            ['name' => 'name', 'type' => 'tr', 'required' => true],
            ['name' => 'capital', 'type' => 'tr'],
            ['name' => 'summary', 'type' => 'trtext'],
            ['name' => 'climate', 'type' => 'trtext'],
            ['name' => 'safety', 'type' => 'trtext'],
            ['name' => 'customs', 'type' => 'trtext'],
            ['name' => 'local_transport', 'type' => 'trtext'],
            ['name' => 'continent', 'type' => 'select', 'options' => Country::CONTINENTS, 'rules' => 'required'],
            ['name' => 'languages', 'type' => 'text', 'rules' => 'nullable|string|max:190'],
            ['name' => 'currency_code', 'type' => 'text', 'rules' => 'nullable|alpha|size:3'],
            ['name' => 'timezone', 'type' => 'text', 'rules' => 'nullable|string|max:60'],
            ['name' => 'best_season', 'type' => 'multi', 'options' => ['spring', 'summer', 'autumn', 'winter'], 'csv' => true],
            ['name' => 'travel_types', 'type' => 'multi', 'options' => Country::TRAVEL_TYPES],
            ['name' => 'local_apps', 'type' => 'csv'],
            ['name' => 'emergency_numbers', 'type' => 'kv'],
            ['name' => 'daily_budget_budget', 'type' => 'number', 'step' => '0.01', 'rules' => 'nullable|numeric|min:0'],
            ['name' => 'daily_budget_comfort', 'type' => 'number', 'step' => '0.01', 'rules' => 'nullable|numeric|min:0'],
            ['name' => 'daily_budget_luxury', 'type' => 'number', 'step' => '0.01', 'rules' => 'nullable|numeric|min:0'],
            ...$latlng,
            ['name' => 'image', 'type' => 'image'],
            ['name' => 'popularity', 'type' => 'number', 'rules' => 'nullable|integer|min:0', 'default' => 0],
            ...$flags,
        ],
    ],
    'cities' => [
        'model' => City::class, 'columns' => ['name:tr', 'country_id:relation', 'popularity'], 'order' => 'popularity',
        'fields' => [
            ['name' => 'country_id', 'type' => 'relation', 'model' => Country::class, 'rules' => 'required|exists:countries,id'],
            ['name' => 'slug', 'type' => 'text', 'rules' => 'required|alpha_dash|max:120', 'unique' => 'cities'],
            ['name' => 'name', 'type' => 'tr', 'required' => true],
            ['name' => 'summary', 'type' => 'trtext'],
            ...$latlng,
            ['name' => 'image', 'type' => 'image'],
            ['name' => 'popularity', 'type' => 'number', 'rules' => 'nullable|integer|min:0', 'default' => 0],
            ...$flags,
        ],
    ],
    'airports' => [
        'model' => Airport::class, 'columns' => ['name', 'iata', 'city_id:relation'],
        'fields' => [
            ['name' => 'city_id', 'type' => 'relation', 'model' => City::class, 'rules' => 'required|exists:cities,id'],
            ['name' => 'name', 'type' => 'text', 'rules' => 'required|string|max:160'],
            ['name' => 'iata', 'type' => 'text', 'rules' => 'nullable|alpha|size:3'],
            ...$latlng,
        ],
    ],
    'attractions' => [
        'model' => Attraction::class, 'columns' => ['name:tr', 'category', 'city_id:relation', 'entrance_usd'], 'order' => 'popularity',
        'fields' => array_merge($placeBase('attractions'), [
            ['name' => 'category', 'type' => 'select', 'options' => Attraction::CATEGORIES, 'rules' => 'required'],
            ['name' => 'opening_hours', 'type' => 'text', 'rules' => 'nullable|string|max:190'],
            ['name' => 'entrance_usd', 'type' => 'number', 'step' => '0.01', 'rules' => 'nullable|numeric|min:0'],
            ['name' => 'duration_min', 'type' => 'number', 'rules' => 'nullable|integer|min:0|max:2000'],
            ['name' => 'transport_info', 'type' => 'text', 'rules' => 'nullable|string|max:255'],
        ]),
    ],
    'hotels' => [
        'model' => Hotel::class, 'columns' => ['name:tr', 'type', 'city_id:relation', 'price_usd'], 'order' => 'popularity',
        'fields' => array_merge($placeBase('hotels'), [
            ['name' => 'type', 'type' => 'select', 'options' => Hotel::TYPES, 'rules' => 'required'],
            ['name' => 'price_usd', 'type' => 'number', 'step' => '0.01', 'rules' => 'nullable|numeric|min:0'],
            ['name' => 'stars', 'type' => 'number', 'rules' => 'nullable|integer|between:1,5'],
            ['name' => 'facilities', 'type' => 'csv'],
            ['name' => 'checkin', 'type' => 'text', 'rules' => 'nullable|string|max:20'],
            ['name' => 'checkout', 'type' => 'text', 'rules' => 'nullable|string|max:20'],
            ['name' => 'airport_km', 'type' => 'number', 'step' => '0.1', 'rules' => 'nullable|numeric|min:0'],
            ['name' => 'booking_url', 'type' => 'url', 'rules' => 'nullable|url|max:255'],
        ]),
    ],
    'restaurants' => [
        'model' => Restaurant::class, 'columns' => ['name:tr', 'category', 'city_id:relation', 'avg_meal_usd'], 'order' => 'popularity',
        'fields' => array_merge($placeBase('restaurants'), [
            ['name' => 'category', 'type' => 'select', 'options' => Restaurant::CATEGORIES, 'rules' => 'required'],
            ['name' => 'cuisine', 'type' => 'text', 'rules' => 'nullable|string|max:120'],
            ['name' => 'is_uzbek', 'type' => 'bool'],
            ['name' => 'is_halal', 'type' => 'bool'],
            ['name' => 'avg_meal_usd', 'type' => 'number', 'step' => '0.01', 'rules' => 'nullable|numeric|min:0'],
            ['name' => 'opening_hours', 'type' => 'text', 'rules' => 'nullable|string|max:190'],
            ['name' => 'phone', 'type' => 'text', 'rules' => 'nullable|string|max:40'],
        ]),
    ],
    'airlines' => [
        'model' => Airline::class, 'columns' => ['name', 'code', 'hub', 'rating'], 'order' => 'name',
        'fields' => [
            ['name' => 'slug', 'type' => 'text', 'rules' => 'required|alpha_dash|max:100', 'unique' => 'airlines'],
            ['name' => 'name', 'type' => 'text', 'rules' => 'required|string|max:120'],
            ['name' => 'code', 'type' => 'text', 'rules' => 'nullable|alpha_num|max:3'],
            ['name' => 'description', 'type' => 'trtext'],
            ['name' => 'website', 'type' => 'url', 'rules' => 'nullable|url|max:255'],
            ['name' => 'baggage_url', 'type' => 'url', 'rules' => 'nullable|url|max:255'],
            ['name' => 'logo', 'type' => 'image'],
            ['name' => 'hub', 'type' => 'text', 'rules' => 'nullable|string|max:120'],
            ['name' => 'rating', 'type' => 'number', 'step' => '0.01', 'rules' => 'nullable|numeric|between:0,5'],
            ...$flags,
        ],
    ],
    'flights' => [
        'model' => Flight::class, 'columns' => ['airline_id:relation', 'from_city', 'to_city_id:relation', 'est_price_usd'],
        'fields' => [
            ['name' => 'airline_id', 'type' => 'relation', 'model' => Airline::class, 'rules' => 'required|exists:airlines,id'],
            ['name' => 'from_city', 'type' => 'text', 'rules' => 'required|string|max:100'],
            ['name' => 'to_city_id', 'type' => 'relation', 'model' => City::class, 'rules' => 'required|exists:cities,id'],
            ['name' => 'departure_time', 'type' => 'time', 'rules' => 'nullable|date_format:H:i'],
            ['name' => 'arrival_time', 'type' => 'time', 'rules' => 'nullable|date_format:H:i'],
            ['name' => 'duration_min', 'type' => 'number', 'rules' => 'nullable|integer|min:0|max:3000'],
            ['name' => 'is_direct', 'type' => 'bool', 'default' => true],
            ['name' => 'transit', 'type' => 'text', 'rules' => 'nullable|string|max:120'],
            ['name' => 'baggage_kg', 'type' => 'number', 'rules' => 'nullable|integer|min:0|max:100'],
            ['name' => 'est_price_usd', 'type' => 'number', 'step' => '0.01', 'rules' => 'nullable|numeric|min:0'],
            ['name' => 'is_sample', 'type' => 'bool', 'default' => true],
        ],
    ],
    'visas' => [
        'model' => VisaRequirement::class, 'columns' => ['country_id:relation', 'status', 'verification_status', 'last_verified_at'],
        'fields' => [
            ['name' => 'country_id', 'type' => 'relation', 'model' => Country::class, 'rules' => 'required|exists:countries,id', 'unique' => 'visa_requirements'],
            ['name' => 'status', 'type' => 'select', 'options' => VisaRequirement::STATUSES, 'rules' => 'required'],
            ['name' => 'max_stay_days', 'type' => 'number', 'rules' => 'nullable|integer|min:0|max:3650'],
            ['name' => 'passport_validity', 'type' => 'text', 'rules' => 'nullable|string|max:190'],
            ['name' => 'documents', 'type' => 'trtext'],
            ['name' => 'notes', 'type' => 'trtext'],
            ['name' => 'insurance_required', 'type' => 'bool'],
            ['name' => 'official_url', 'type' => 'url', 'rules' => 'nullable|url|max:255'],
            ['name' => 'source_url', 'type' => 'url', 'rules' => 'nullable|url|max:255'],
            ['name' => 'last_verified_at', 'type' => 'date', 'rules' => 'nullable|date'],
            ['name' => 'verification_status', 'type' => 'select', 'options' => ['verified', 'unverified', 'demo'], 'rules' => 'required'],
        ],
    ],
    'transport' => [
        'model' => TransportService::class, 'columns' => ['name', 'type', 'city_id:relation', 'start_fare_usd'],
        'fields' => [
            ['name' => 'city_id', 'type' => 'relation', 'model' => City::class, 'rules' => 'required|exists:cities,id'],
            ['name' => 'type', 'type' => 'select', 'options' => TransportService::TYPES, 'rules' => 'required'],
            ['name' => 'name', 'type' => 'text', 'rules' => 'required|string|max:160'],
            ['name' => 'description', 'type' => 'trtext'],
            ['name' => 'website', 'type' => 'url', 'rules' => 'nullable|url|max:255'],
            ['name' => 'start_fare_usd', 'type' => 'number', 'step' => '0.01', 'rules' => 'nullable|numeric|min:0'],
            ['name' => 'per_km_usd', 'type' => 'number', 'step' => '0.01', 'rules' => 'nullable|numeric|min:0'],
            ['name' => 'price_verified', 'type' => 'bool'],
            ['name' => 'is_demo', 'type' => 'bool'],
        ],
    ],
    'guides' => [
        'model' => TravelGuide::class, 'columns' => ['title:tr', 'slug', 'is_published'],
        'fields' => [
            ['name' => 'slug', 'type' => 'text', 'rules' => 'required|alpha_dash|max:120', 'unique' => 'travel_guides'],
            ['name' => 'title', 'type' => 'tr', 'required' => true],
            ['name' => 'excerpt', 'type' => 'trtext'],
            ['name' => 'body', 'type' => 'trtext'],
            ['name' => 'image', 'type' => 'image'],
            ['name' => 'is_published', 'type' => 'bool', 'default' => true],
        ],
    ],
];
