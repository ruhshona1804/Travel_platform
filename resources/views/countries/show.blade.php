@extends('layouts.app')
@section('title', $country->tr('name'))
@section('description', \Illuminate\Support\Str::limit(strip_tags((string) $country->tr('summary')), 155))
@section('image', $country->imageUrl())
@section('breadcrumbs') / <a href="{{ route('countries.index') }}" class="hover:text-ocean-600">{{ __('ui.nav_countries') }}</a> / <span>{{ $country->tr('name') }}</span> @endsection
@push('head')
<script type="application/ld+json">{!! json_encode(['@context' => 'https://schema.org', '@type' => 'Country', 'name' => $country->tr('name'), 'url' => url()->current()], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
@endpush
@section('content')
<div class="container-x py-6">
    <div class="card overflow-hidden">
        <x-cover :model="$country" class="h-56 sm:h-72" />
        <div class="flex flex-wrap items-start justify-between gap-4 p-6">
            <div>
                <h1 class="text-3xl font-extrabold sm:text-4xl">{{ $country->flag() }} {{ $country->tr('name') }}</h1>
                <p class="mt-1 text-slate-500">{{ __('ui.capital') }}: {{ $country->tr('capital') }} · {{ __("ui.continent_{$country->continent}") }}</p>
                @if($country->is_demo)<span class="badge badge-demo mt-2">{{ __('ui.demo_data') }}</span>@endif
            </div>
            <div class="flex gap-2"><x-fav type="country" :model="$country" /><a class="btn btn-accent btn-sm" href="{{ route('planner', ['country' => $country->slug]) }}">{{ __('ui.plan_trip') }}</a></div>
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            @foreach(['summary' => 'general_info', 'climate' => 'climate', 'safety' => 'safety', 'customs' => 'customs', 'local_transport' => 'local_transport'] as $f => $label)
                @if($country->tr($f))<section class="card p-6"><h2 class="mb-2 text-xl font-bold">{{ __("ui.$label") }}</h2><div class="prose-lite text-slate-700 dark:text-slate-300">{!! nl2br(e($country->tr($f))) !!}</div></section>@endif
            @endforeach

            @if($country->cities->isNotEmpty())
            <section><h2 class="mb-3 text-xl font-bold">{{ __('ui.popular_cities') }}</h2>
                <div class="grid gap-4 sm:grid-cols-2">@foreach($country->cities as $c)<a href="{{ route('cities.show', $c) }}" class="card card-hover group block overflow-hidden"><x-cover :model="$c" class="h-32" emoji="🏙️" /><div class="p-3 font-semibold">{{ $c->tr('name') }}</div></a>@endforeach</div></section>
            @endif

            @if($attractions->isNotEmpty())<section><div class="mb-3 flex justify-between"><h2 class="text-xl font-bold">{{ __('ui.nav_attractions') }}</h2><a class="text-sm text-ocean-600" href="{{ route('attractions.index', ['country' => $country->slug]) }}">{{ __('ui.see_all') }} →</a></div><div class="grid gap-4 sm:grid-cols-2">@foreach($attractions->take(4) as $a)<x-place-card :item="$a" kind="attractions" />@endforeach</div></section>@endif
            @if($hotels->isNotEmpty())<section><div class="mb-3 flex justify-between"><h2 class="text-xl font-bold">{{ __('ui.nav_hotels') }}</h2><a class="text-sm text-ocean-600" href="{{ route('hotels.index', ['country' => $country->slug]) }}">{{ __('ui.see_all') }} →</a></div><div class="grid gap-4 sm:grid-cols-2">@foreach($hotels->take(4) as $h)<x-place-card :item="$h" kind="hotels" />@endforeach</div></section>@endif
            @if($restaurants->isNotEmpty())<section><div class="mb-3 flex justify-between"><h2 class="text-xl font-bold">{{ __('ui.nav_restaurants') }}</h2><a class="text-sm text-ocean-600" href="{{ route('restaurants.index', ['country' => $country->slug]) }}">{{ __('ui.see_all') }} →</a></div><div class="grid gap-4 sm:grid-cols-2">@foreach($restaurants->take(4) as $r)<x-place-card :item="$r" kind="restaurants" />@endforeach</div></section>@endif
            @if($uzbek->isNotEmpty())<section class="card border-emerald-300/60 p-6"><h2 class="mb-3 text-xl font-bold">🥘 {{ __('ui.uzbek_food_title') }}</h2><div class="grid gap-3 sm:grid-cols-2">@foreach($uzbek as $r)<a href="{{ route('restaurants.show', $r) }}" class="rounded-xl border border-slate-200 p-3 hover:border-emerald-400 dark:border-white/10"><div class="font-semibold">{{ $r->tr('name') }}</div><div class="text-xs text-slate-500">{{ $r->city->tr('name') }}</div></a>@endforeach</div></section>@endif

            @if($transport->isNotEmpty())<section class="card p-6"><h2 class="mb-3 text-xl font-bold">{{ __('ui.transport') }}</h2>@include('partials.transport-list', ['transport' => $transport, 'showCity' => true])</section>@endif
        </div>

        <aside class="space-y-6">
            <section class="card p-6">
                <h2 class="mb-3 text-lg font-bold">{{ __('ui.quick_facts') }}</h2>
                <dl class="space-y-2 text-sm">
                    @foreach([['languages_label', $country->languages], ['currency', $country->currency_code], ['timezone', $country->timezone]] as [$k, $v])@if($v)<div class="flex justify-between gap-3"><dt class="text-slate-500">{{ __("ui.$k") }}</dt><dd class="text-right font-medium">{{ $v }}</dd></div>@endif @endforeach
                    @if($country->best_season)<div class="flex justify-between gap-3"><dt class="text-slate-500">{{ __('ui.best_season') }}</dt><dd class="text-right font-medium">{{ collect(explode(',', $country->best_season))->map(fn ($s) => __("ui.season_$s"))->implode(', ') }}</dd></div>@endif
                    @if($country->currency_code && $country->currency_code !== 'USD')<div class="flex justify-between gap-3"><dt class="text-slate-500">1 USD</dt><dd class="font-medium">{{ $currencyService->formatIn($currencyService->fromUsd(1, $country->currency_code), $country->currency_code) }}</dd></div>@endif
                </dl>
            </section>

            <section class="card p-6">
                <h2 class="mb-3 text-lg font-bold">{{ __('ui.avg_expenses') }}</h2>
                <div class="space-y-2 text-sm">@foreach(['budget' => 'daily_budget_budget', 'comfortable' => 'daily_budget_comfort', 'luxury' => 'daily_budget_luxury'] as $s => $col)@if($country->$col)<div class="flex justify-between"><span class="text-slate-500">{{ __("ui.style_$s") }}</span><x-price :usd="$country->$col" /></div>@endif @endforeach</div>
                <p class="mt-3 text-xs text-slate-500">{{ __('ui.per_person_day_estimate') }}</p>
            </section>

            <section class="card p-6" id="visa">
                <h2 class="mb-3 text-lg font-bold">🛂 {{ __('ui.visa_for_uz') }}</h2>
                @include('partials.visa-box', ['visa' => $country->visa])
            </section>

            @if($country->emergency_numbers)<section class="card p-6"><h2 class="mb-3 text-lg font-bold">🚨 {{ __('ui.emergency') }}</h2><dl class="space-y-1 text-sm">@foreach($country->emergency_numbers as $k => $v)<div class="flex justify-between"><dt class="text-slate-500">{{ $k }}</dt><dd class="font-semibold">{{ $v }}</dd></div>@endforeach</dl></section>@endif
            @if($country->local_apps)<section class="card p-6"><h2 class="mb-3 text-lg font-bold">📱 {{ __('ui.local_apps') }}</h2><div class="flex flex-wrap gap-2">@foreach($country->local_apps as $a)<span class="chip">{{ $a }}</span>@endforeach</div></section>@endif

            @php $airports = $country->cities->flatMap->airports; @endphp
            @if($airports->isNotEmpty())<section class="card p-6"><h2 class="mb-3 text-lg font-bold">✈️ {{ __('ui.airports') }}</h2><ul class="space-y-1 text-sm">@foreach($airports as $ap)<li>{{ $ap->name }} @if($ap->iata)<span class="badge badge-info">{{ $ap->iata }}</span>@endif</li>@endforeach</ul><a class="mt-3 inline-block text-sm text-ocean-600" href="{{ route('flights.compare') }}">{{ __('ui.compare_flights') }} →</a></section>@endif
        </aside>
    </div>

    <section class="mt-8"><h2 class="mb-3 text-xl font-bold">{{ __('ui.nav_map') }}</h2><div class="card overflow-hidden p-2"><x-map :points="$points" height="h-96" /></div></section>
</div>
@endsection
