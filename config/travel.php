<?php

return [
    'name' => env('APP_NAME', 'Travel Platform'),

    'locales' => [
        'uz' => ['name' => "O'zbekcha", 'flag' => '🇺🇿'],
        'ru' => ['name' => 'Русский', 'flag' => '🇷🇺'],
        'en' => ['name' => 'English', 'flag' => '🇬🇧'],
        'kk' => ['name' => 'Қазақша', 'flag' => '🇰🇿'],
        'ky' => ['name' => 'Кыргызча', 'flag' => '🇰🇬'],
        'kaa' => ['name' => 'Qaraqalpaqsha', 'flag' => '🏳️'],
    ],

    // Foydalanuvchi sayti dastlab shu valyutada ko'radi; UZS har doim qo'shimcha ko'rsatiladi.
    'default_currency' => 'USD',
    'base_currency' => 'UZS',

    'exchange' => [
        'provider' => env('EXCHANGE_PROVIDER', 'open_er_api'),
        'api_key' => env('EXCHANGE_API_KEY'),
        'stale_after_hours' => 48,
    ],

    'ai' => [
        'provider' => env('AI_PROVIDER', 'anthropic'), // anthropic | none
        'api_key' => env('AI_API_KEY'),
        'model' => env('AI_MODEL', 'claude-sonnet-5-5'),
        'timeout' => 40,
    ],

    // Birinchi Super Admin (faqat seeder o'qiydi). Qiymatlar .env dan olinadi, kodga yozilmaydi.
    'admin' => ['name' => env('ADMIN_NAME', 'Super Admin'), 'email' => env('ADMIN_EMAIL'), 'password' => env('ADMIN_PASSWORD')],

    'google' => ['enabled' => (bool) env('GOOGLE_CLIENT_ID')],

    // Jonli parvoz ma'lumotlari provayderi ulanmagan bo'lsa "none" qoladi.
    'flights' => ['provider' => env('FLIGHT_PROVIDER', 'none')],

    'map' => [
        'tiles' => 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
        'attribution' => '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    ],
];
