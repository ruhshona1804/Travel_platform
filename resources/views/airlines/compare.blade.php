@extends('layouts.app')
@section('title', __('ui.compare_flights'))
@section('breadcrumbs') / <a href="{{ route('airlines.index') }}" class="hover:text-ocean-600">{{ __('ui.nav_airlines') }}</a> / <span>{{ __('ui.compare_flights') }}</span> @endsection
@section('content')
<div class="container-x py-8">
    <h1 class="text-3xl font-bold">{{ __('ui.compare_flights') }}</h1>
    @unless($live)<p class="mt-3 rounded-xl bg-amber-50 p-3 text-sm text-amber-900 dark:bg-amber-400/10 dark:text-amber-200">⚠️ {{ __('ui.flights_no_live') }}</p>@endunless
    <form method="GET" class="card mt-5 grid gap-3 p-4 sm:grid-cols-4">
        <div class="sm:col-span-2"><label class="label" for="city">{{ __('ui.destination') }}</label><select id="city" name="city" class="input" required><option value="">—</option>@foreach($cities as $c)<option value="{{ $c->id }}" @selected($city?->id === $c->id)>{{ $c->tr('name') }}, {{ $c->country->tr('name') }}</option>@endforeach</select></div>
        <div><label class="label" for="sort">{{ __('ui.sort') }}</label><select id="sort" name="sort" class="input">@foreach(['price' => 'sort_price', 'duration' => 'duration', 'departure' => 'departure'] as $v => $l)<option value="{{ $v }}" @selected($sort === $v)>{{ __("ui.$l") }}</option>@endforeach</select></div>
        <div class="flex items-end gap-3"><label class="flex items-center gap-2 text-sm"><input type="checkbox" name="direct" value="1" @checked(request()->boolean('direct'))> {{ __('ui.direct') }}</label><button class="btn btn-primary ml-auto">{{ __('ui.compare') }}</button></div>
    </form>
    <div class="mt-6">@if($city)@include('partials.flight-table', ['flights' => $flights])@else<x-empty :text="__('ui.choose_destination')" />@endif</div>
    <div class="mt-6 flex flex-wrap gap-2 text-sm"><span class="text-slate-500">{{ __('ui.official_sites') }}:</span>@foreach(\App\Models\Airline::where('is_published', true)->whereNotNull('website')->get() as $a)<a class="chip" href="{{ $a->website }}" target="_blank" rel="noopener noreferrer">{{ $a->name }} ↗</a>@endforeach</div>
</div>
@endsection
