@extends('layouts.app')
@section('title', __('ui.nav_map'))
@section('content')
<div class="container-x py-6" x-data="fullMap({ url: '{{ route('map.data') }}' })">
    <div class="mb-4 flex flex-wrap items-end justify-between gap-3"><h1 class="text-3xl font-bold">🗺️ {{ __('ui.nav_map') }}</h1>
        <form @submit.prevent="load" class="flex gap-2"><input x-model="q" class="input w-56" placeholder="{{ __('ui.search_place') }}" aria-label="{{ __('ui.search_place') }}"><button class="btn btn-primary">{{ __('ui.search') }}</button></form></div>
    <div class="mb-3 flex flex-wrap gap-2">
        @foreach(['city' => ['🏙️', 'nav_cities'], 'attraction' => ['📍', 'nav_attractions'], 'hotel' => ['🛏️', 'nav_hotels'], 'restaurant' => ['🍽️', 'nav_restaurants'], 'uzbek' => ['🥘', 'nav_uzbek_food'], 'airport' => ['✈️', 'airports']] as $k => [$e, $l])
            <button class="chip" :class="active.includes('{{ $k }}') ? 'chip-active' : ''" @click="toggle('{{ $k }}')">{{ $e }} {{ __("ui.$l") }}</button>
        @endforeach
    </div>
    <div class="grid gap-4 lg:grid-cols-4">
        <div class="relative card overflow-hidden p-2 lg:col-span-3"><div x-ref="map" class="h-[70vh] min-h-[420px] w-full" data-tiles="{{ config('travel.map.tiles') }}" data-attribution="{{ config('travel.map.attribution') }}" data-open="{{ __('ui.open') }}"></div>
            <div x-show="loading" class="absolute inset-0 z-[500] grid place-items-center bg-white/50 dark:bg-night/50"><div class="skeleton h-10 w-40"></div></div></div>
        <aside class="card p-4">
            <h2 class="font-bold">{{ __('ui.route_planner') }}</h2>
            <p class="mt-1 text-xs text-slate-500">{{ __('ui.route_hint') }}</p>
            <label class="mt-3 flex items-center gap-2 text-sm"><input type="checkbox" x-model="selecting"> {{ __('ui.select_points') }}</label>
            <ol class="mt-3 space-y-1 text-sm"><template x-for="(p, i) in selected" :key="p.id"><li class="flex items-center justify-between gap-2 rounded-lg bg-slate-50 px-2 py-1.5 dark:bg-white/5"><span><b x-text="i + 1"></b>. <span x-text="p.name"></span></span><button class="text-red-500" @click="remove(i)" aria-label="{{ __('ui.delete') }}">✕</button></li></template></ol>
            <div x-show="selected.length > 1" x-cloak class="mt-3 text-sm"><div class="font-semibold">{{ __('ui.total_distance') }}: <span x-text="km.toFixed(1)"></span> km</div><p class="text-xs text-slate-500">{{ __('ui.straight_line') }}</p>
                <a :href="directionsUrl()" target="_blank" rel="noopener noreferrer" class="btn btn-accent btn-sm mt-2 w-full">{{ __('ui.get_directions') }} ↗</a></div>
            <button x-show="selected.length" x-cloak @click="clear" class="btn btn-ghost btn-sm mt-2 w-full">{{ __('ui.clear') }}</button>
            <p class="mt-4 text-xs text-slate-500" x-text="points.length + ' {{ __('ui.markers') }}'"></p>
        </aside>
    </div>
</div>
@endsection
