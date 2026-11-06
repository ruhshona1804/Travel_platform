<?php

use App\Models\Setting;
use App\Services\Currency\CurrencyService;

if (! function_exists('price')) {
    function price(?float $usd): string
    {
        return app(CurrencyService::class)->format($usd === null ? null : (float) $usd);
    }
}

if (! function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        try {
            return Setting::get($key, $default) ?? $default;
        } catch (\Throwable) {
            return $default;
        }
    }
}

if (! function_exists('app_name')) {
    function app_name(): string { return setting('app_name', config('travel.name')); }
}

if (! function_exists('img')) {
    function img(?string $path): ?string
    {
        if (! $path) return null;
        return str_starts_with($path, 'http') ? $path : asset('storage/'.$path);
    }
}

if (! function_exists('enabled_locales')) {
    function enabled_locales(): array
    {
        $enabled = json_decode((string) setting('enabled_locales', ''), true);
        $all = config('travel.locales');
        return is_array($enabled) ? array_intersect_key($all, array_flip($enabled)) : $all;
    }
}

if (! function_exists('section_visible')) {
    function section_visible(string $name): bool
    {
        return ! in_array($name, (array) json_decode((string) setting('hidden_sections', '[]'), true), true);
    }
}
