@extends('layouts.app')
@section('title', __('ui.nav_airlines'))
@section('breadcrumbs') / <span>{{ __('ui.nav_airlines') }}</span> @endsection
@section('content')
<div class="container-x py-8">
    <div class="flex flex-wrap items-end justify-between gap-3"><div><h1 class="text-3xl font-bold">{{ __('ui.nav_airlines') }}</h1><p class="text-slate-500">{{ __('ui.airlines_sub') }}</p></div><a class="btn btn-primary" href="{{ route('flights.compare') }}">{{ __('ui.compare_flights') }}</a></div>
    <p class="mt-4 rounded-xl bg-amber-50 p-3 text-sm text-amber-900 dark:bg-amber-400/10 dark:text-amber-200">ℹ️ {{ __('ui.independent_notice') }}</p>
    <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($airlines as $a)
        <a href="{{ route('airlines.show', $a) }}" class="card card-hover block p-5">
            <div class="flex items-center gap-3"><div class="grid h-12 w-12 place-items-center overflow-hidden rounded-xl bg-ocean-50 text-lg font-extrabold text-ocean-700 dark:bg-white/10">@if($a->logo)<img src="{{ img($a->logo) }}" alt="{{ $a->name }}" class="h-full w-full object-contain">@else{{ $a->code ?: '✈' }}@endif</div>
                <div><h2 class="font-bold">{{ $a->name }}</h2><div class="text-xs text-slate-500">{{ $a->hub }}</div></div><div class="ml-auto"><x-stars :value="$a->rating" /></div></div>
            <p class="mt-3 line-clamp-3 text-sm text-slate-600 dark:text-slate-300">{{ $a->tr('description') }}</p>
        </a>@endforeach
    </div>
</div>
@endsection
