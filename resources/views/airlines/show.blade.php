@extends('layouts.app')
@section('title', $airline->name)
@section('breadcrumbs') / <a href="{{ route('airlines.index') }}" class="hover:text-ocean-600">{{ __('ui.nav_airlines') }}</a> / <span>{{ $airline->name }}</span> @endsection
@section('content')
<div class="container-x py-8">
    <div class="card p-6 sm:p-8">
        <div class="flex flex-wrap items-center gap-4"><div class="grid h-16 w-16 place-items-center overflow-hidden rounded-2xl bg-ocean-50 text-xl font-extrabold text-ocean-700 dark:bg-white/10">@if($airline->logo)<img src="{{ img($airline->logo) }}" alt="{{ $airline->name }}" class="h-full w-full object-contain">@else{{ $airline->code ?: '✈' }}@endif</div>
            <div><h1 class="text-3xl font-extrabold">{{ $airline->name }}</h1><p class="text-slate-500">{{ $airline->hub }} @if($airline->code)· <span class="badge badge-info">{{ $airline->code }}</span>@endif</p></div><div class="ml-auto"><x-stars :value="$airline->rating" /></div></div>
        <p class="mt-4 max-w-3xl text-slate-700 dark:text-slate-300">{{ $airline->tr('description') }}</p>
        <div class="mt-5 flex flex-wrap gap-3">
            @if($airline->website)<a class="btn btn-primary" href="{{ $airline->website }}" target="_blank" rel="noopener noreferrer">{{ __('ui.official_website') }} ↗</a>@endif
            @if($airline->baggage_url)<a class="btn btn-ghost" href="{{ $airline->baggage_url }}" target="_blank" rel="noopener noreferrer">🧳 {{ __('ui.baggage_policy') }} ↗</a>@endif
        </div>
        @if($airline->is_demo)<p class="mt-4 text-xs text-slate-500">ℹ️ {{ __('ui.airline_demo_note') }}</p>@endif
    </div>
    <h2 class="mb-3 mt-8 text-xl font-bold">{{ __('ui.routes') }}</h2>
    @include('partials.flight-table', ['flights' => $airline->flights])
</div>
@endsection
