@props(['points' => [], 'height' => 'h-80', 'route' => false, 'zoom' => null])
<div x-data="mapView(@js($points), @js(['route' => $route, 'zoom' => $zoom]))" class="relative">
    <div x-ref="map" class="{{ $height }} w-full" data-tiles="{{ config('travel.map.tiles') }}" data-attribution="{{ config('travel.map.attribution') }}" data-open="{{ __('ui.open') }}" role="application" aria-label="{{ __('ui.nav_map') }}"></div>
    <div x-show="empty" x-cloak class="absolute inset-0 z-[500] grid place-items-center rounded-2xl bg-white/80 p-6 text-center text-sm text-slate-600 dark:bg-night/80 dark:text-slate-300">{{ __('ui.no_map_data') }}</div>
</div>
