@extends('layouts.app')
@section('title', null)
@section('content')
{{-- HERO --}}
<section class="hero-bg relative overflow-hidden text-white">
    <div class="pointer-events-none absolute inset-0 opacity-30" aria-hidden="true">
        <svg viewBox="0 0 1200 400" class="absolute bottom-0 w-full" preserveAspectRatio="none"><path d="M0 300 C200 220 400 380 600 300 S1000 220 1200 300 V400 H0Z" fill="rgba(255,255,255,.08)"/><path d="M0 340 C250 280 450 400 700 330 S1050 280 1200 340 V400 H0Z" fill="rgba(255,255,255,.08)"/></svg>
    </div>
    <div class="container-x relative grid gap-10 py-16 lg:grid-cols-5 lg:py-24" >
        <div class="fade-up lg:col-span-3">
            <span class="badge bg-white/15 text-white backdrop-blur">🧭 {{ __('ui.hero_badge') }}</span>
            <h1 class="mt-4 text-4xl font-extrabold leading-tight sm:text-5xl lg:text-6xl">{{ __('ui.hero_title') }}</h1>
            <p class="mt-5 max-w-2xl text-lg text-ocean-100">{{ __('ui.hero_sub') }}</p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('planner') }}" class="btn btn-accent !px-7 !py-3.5 text-base">{{ __('ui.hero_cta_primary') }}</a>
                <a href="{{ route('countries.index') }}" class="btn !border !border-white/40 !px-7 !py-3.5 text-base text-white hover:bg-white/10">{{ __('ui.hero_cta_secondary') }}</a>
            </div>
            <dl class="mt-10 grid max-w-xl grid-cols-3 gap-4 text-center">
                <div class="glass rounded-2xl p-3 !text-white"><dt class="text-xs text-ocean-100">{{ __('ui.nav_countries') }}</dt><dd class="text-2xl font-bold">{{ $allCountries->count() }}</dd></div>
                <div class="glass rounded-2xl p-3 !text-white"><dt class="text-xs text-ocean-100">{{ __('ui.cities') }}</dt><dd class="text-2xl font-bold">{{ $allCities->count() }}</dd></div>
                <div class="glass rounded-2xl p-3 !text-white"><dt class="text-xs text-ocean-100">{{ __('ui.languages') }}</dt><dd class="text-2xl font-bold">6</dd></div>
            </dl>
        </div>

        {{-- Qidiruv formasi --}}
        <form action="{{ route('countries.index') }}" method="GET" class="glass fade-up self-end rounded-3xl p-5 text-navy shadow-2xl dark:text-slate-100 lg:col-span-2" x-data="suggest('{{ route('search.suggest') }}')" @click.outside="open = false">
            <h2 class="mb-4 text-lg font-bold">{{ __('ui.search_form_title') }}</h2>
            <div class="relative">
                <label class="label" for="hero-q">{{ __('ui.where_to') }}</label>
                <input id="hero-q" name="q" x-model="q" @input="input" @focus="open = true" autocomplete="off" class="input" placeholder="{{ __('ui.search_placeholder') }}">
                <ul x-show="open && items.length" x-cloak class="card absolute z-30 mt-1 max-h-72 w-full overflow-auto p-1 text-sm shadow-xl">
                    <template x-for="(it, i) in items" :key="i">
                        <li><a :href="it.url" class="flex justify-between rounded-lg px-3 py-2 hover:bg-slate-100 dark:hover:bg-white/10"><span x-text="it.label"></span><span class="text-xs text-slate-400" x-text="it.sub"></span></a></li>
                    </template>
                </ul>
            </div>
            <div class="mt-3 grid grid-cols-2 gap-3">
                <div><label class="label" for="hero-budget">{{ __('ui.budget') }}</label>
                    <select id="hero-budget" name="budget" class="input"><option value="">{{ __('ui.any') }}</option>@foreach(['low', 'mid', 'high'] as $b)<option value="{{ $b }}">{{ __("ui.budget_$b") }}</option>@endforeach</select></div>
                <div><label class="label" for="hero-type">{{ __('ui.travel_type') }}</label>
                    <select id="hero-type" name="type" class="input"><option value="">{{ __('ui.any') }}</option>@foreach(\App\Models\Country::TRAVEL_TYPES as $t)<option value="{{ $t }}">{{ __("ui.style_$t") }}</option>@endforeach</select></div>
                <div><label class="label" for="hero-season">{{ __('ui.season') }}</label>
                    <select id="hero-season" name="season" class="input"><option value="">{{ __('ui.any') }}</option>@foreach(['spring', 'summer', 'autumn', 'winter'] as $s)<option value="{{ $s }}">{{ __("ui.season_$s") }}</option>@endforeach</select></div>
                <div><label class="label" for="hero-visa">{{ __('ui.visa') }}</label>
                    <select id="hero-visa" name="visa" class="input"><option value="">{{ __('ui.any') }}</option>@foreach(['visa_free', 'e_visa', 'on_arrival', 'visa_required'] as $v)<option value="{{ $v }}">{{ __("ui.vs_$v") }}</option>@endforeach</select></div>
            </div>
            <button class="btn btn-primary mt-4 w-full !py-3">🔍 {{ __('ui.search') }}</button>
        </form>
    </div>
</section>

<x-section :title="__('ui.popular_countries')" :sub="__('ui.popular_countries_sub')" :href="route('countries.index')">
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">@foreach($countries as $c)<x-country-card :country="$c" />@endforeach</div>
</x-section>

<x-section :title="__('ui.popular_destinations')" :href="route('countries.index')">
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($cities as $c)
            <a href="{{ route('cities.show', $c) }}" class="card card-hover group block overflow-hidden">
                <x-cover :model="$c" class="h-52" emoji="🏙️" />
                <div class="flex items-center justify-between p-4"><div><h3 class="font-bold">{{ $c->tr('name') }}</h3><p class="text-sm text-slate-500">{{ $c->country->flag() }} {{ $c->country->tr('name') }}</p></div><span class="text-ocean-600">→</span></div>
            </a>
        @endforeach
    </div>
</x-section>

@if(section_visible('trending') && $attractions->isNotEmpty())
<x-section :title="__('ui.trending')" :sub="__('ui.trending_sub')" :href="route('attractions.index')">
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">@foreach($attractions as $a)<x-place-card :item="$a" kind="attractions" />@endforeach</div>
</x-section>
@endif

@if(section_visible('budget'))
<x-section :title="__('ui.budget_friendly')" :sub="__('ui.budget_friendly_sub')" :href="route('budget')" :more="__('ui.nav_budget')">
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">@foreach($budgetCountries as $c)<x-country-card :country="$c" />@endforeach</div>
</x-section>
@endif

@if(section_visible('hotels') && $hotels->isNotEmpty())
<x-section :title="__('ui.recommended_hotels')" :href="route('hotels.index')">
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">@foreach($hotels as $h)<x-place-card :item="$h" kind="hotels" />@endforeach</div>
</x-section>
@endif

@if(section_visible('restaurants') && $restaurants->isNotEmpty())
<x-section :title="__('ui.popular_restaurants')" :href="route('restaurants.index')">
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">@foreach($restaurants as $r)<x-place-card :item="$r" kind="restaurants" />@endforeach</div>
    <a href="{{ route('uzbek-food') }}" class="card card-hover mt-6 flex items-center gap-4 border-emerald-300/60 bg-emerald-50 p-5 dark:bg-emerald-400/10"><span class="text-4xl">🥘</span><div><div class="font-bold">{{ __('ui.uzbek_food_title') }}</div><div class="text-sm text-slate-600 dark:text-slate-300">{{ __('ui.uzbek_food_sub') }}</div></div><span class="ml-auto">→</span></a>
</x-section>
@endif

<x-section :title="__('ui.inspiration')" :sub="__('ui.inspiration_sub')">
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach([['budget','💸'],['historical','🏛️'],['family','👨‍👩‍👧'],['adventure','🏔️'],['shopping','🛍️'],['religious','🕌'],['luxury','💎'],['comfortable','🛋️']] as [$t, $e])
            <a href="{{ route('countries.index', ['type' => $t]) }}" class="card card-hover flex items-center gap-3 p-4"><span class="grid h-12 w-12 place-items-center rounded-xl bg-ocean-50 text-2xl dark:bg-white/10">{{ $e }}</span><span class="font-semibold">{{ __("ui.style_$t") }}</span></a>
        @endforeach
    </div>
</x-section>

@if(section_visible('map'))
<x-section :title="__('ui.map_preview')" :href="route('map')" :more="__('ui.open_map')">
    <div class="card overflow-hidden p-2"><x-map :points="$mapPoints" height="h-96" /></div>
</x-section>
@endif

@if(section_visible('planner'))
<section class="container-x mt-16">
    <div class="hero-bg relative overflow-hidden rounded-3xl p-8 text-white sm:p-12">
        <div class="relative grid items-center gap-8 md:grid-cols-2">
            <div><span class="badge bg-white/15 text-white">🤖 AI</span>
                <h2 class="mt-3 text-3xl font-extrabold">{{ __('ui.planner_title') }}</h2>
                <p class="mt-3 text-ocean-100">{{ __('ui.planner_sub') }}</p>
                <a href="{{ route('planner') }}" class="btn btn-accent mt-6">{{ __('ui.planner_cta') }}</a></div>
            <ul class="grid gap-2 text-sm">@foreach(['flights','hotels','restaurants','attractions','transport','itinerary','budget','routes'] as $f)<li class="glass rounded-xl px-4 py-2 !text-white">✔ {{ __("ui.planner_feat_$f") }}</li>@endforeach</ul>
        </div>
    </div>
</section>
@endif

@if(section_visible('guides') && $guides->isNotEmpty())
<x-section :title="__('ui.travel_tips')" :href="route('guides.index')">
    <div class="grid gap-5 md:grid-cols-3">@foreach($guides as $g)
        <a href="{{ route('guides.show', $g) }}" class="card card-hover block overflow-hidden"><x-cover :model="$g" class="h-36" emoji="📖" /><div class="p-4"><h3 class="font-bold">{{ $g->tr('title') }}</h3><p class="mt-1 line-clamp-2 text-sm text-slate-500">{{ $g->tr('excerpt') }}</p></div></a>
    @endforeach</div>
</x-section>
@endif
@endsection
