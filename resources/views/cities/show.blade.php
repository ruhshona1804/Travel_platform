@extends('layouts.app')
@section('title', $city->tr('name').', '.$city->country->tr('name'))
@section('description', \Illuminate\Support\Str::limit(strip_tags((string) $city->tr('summary')), 155))
@section('image', $city->imageUrl())
@section('breadcrumbs') / <a href="{{ route('countries.index') }}" class="hover:text-ocean-600">{{ __('ui.nav_countries') }}</a> / <a href="{{ route('countries.show', $city->country) }}" class="hover:text-ocean-600">{{ $city->country->tr('name') }}</a> / <span>{{ $city->tr('name') }}</span> @endsection
@push('head')
<script type="application/ld+json">{!! json_encode(['@context' => 'https://schema.org', '@type' => 'City', 'name' => $city->tr('name'), 'containedInPlace' => ['@type' => 'Country', 'name' => $city->country->tr('name')]], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
@endpush
@section('content')
<div class="container-x py-6" x-data="{ tab: 'attractions' }">
    <div class="card overflow-hidden"><x-cover :model="$city" class="h-56 sm:h-64" emoji="🏙️" />
        <div class="flex flex-wrap items-center justify-between gap-3 p-6"><div><h1 class="text-3xl font-extrabold">{{ $city->tr('name') }}</h1><a href="{{ route('countries.show', $city->country) }}" class="text-slate-500 hover:text-ocean-600">{{ $city->country->flag() }} {{ $city->country->tr('name') }}</a></div>
        <a class="btn btn-accent" href="{{ route('planner', ['country' => $city->country->slug, 'city' => $city->slug]) }}">{{ __('ui.plan_trip') }}</a></div>
        @if($city->tr('summary'))<p class="px-6 pb-6 text-slate-700 dark:text-slate-300">{{ $city->tr('summary') }}</p>@endif
    </div>

    <div class="mt-6 flex flex-wrap gap-2" role="tablist">
        @foreach(['attractions' => 'nav_attractions', 'hotels' => 'nav_hotels', 'restaurants' => 'nav_restaurants', 'transport' => 'transport', 'flights' => 'flights', 'visa' => 'visa'] as $k => $l)
            <button role="tab" @click="tab = '{{ $k }}'" :class="tab === '{{ $k }}' ? 'chip-active' : ''" class="chip">{{ __("ui.$l") }}</button>
        @endforeach
    </div>

    <div class="mt-5">
        <div x-show="tab === 'attractions'">@if($attractions->isEmpty())<x-empty />@else<div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">@foreach($attractions as $a)<x-place-card :item="$a" kind="attractions" />@endforeach</div>@endif</div>
        <div x-show="tab === 'hotels'" x-cloak>@if($hotels->isEmpty())<x-empty />@else<div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">@foreach($hotels as $a)<x-place-card :item="$a" kind="hotels" />@endforeach</div>@endif</div>
        <div x-show="tab === 'restaurants'" x-cloak>@if($restaurants->isEmpty())<x-empty />@else<div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">@foreach($restaurants as $a)<x-place-card :item="$a" kind="restaurants" />@endforeach</div>@endif</div>
        <div x-show="tab === 'transport'" x-cloak>@if($transport->isEmpty())<x-empty />@else<div class="card p-5">@include('partials.transport-list', ['transport' => $transport])</div>@endif</div>
        <div x-show="tab === 'flights'" x-cloak>@include('partials.flight-table', ['flights' => $flightList])<a class="mt-3 inline-block text-sm text-ocean-600" href="{{ route('flights.compare', ['city' => $city->id]) }}">{{ __('ui.compare_flights') }} →</a></div>
        <div x-show="tab === 'visa'" x-cloak class="card max-w-xl p-6">@include('partials.visa-box', ['visa' => $city->country->visa])</div>
    </div>

    <section class="mt-8"><h2 class="mb-3 text-xl font-bold">{{ __('ui.nav_map') }}</h2><div class="card overflow-hidden p-2"><x-map :points="$points" height="h-[28rem]" /></div></section>
</div>
@endsection
