@extends('layouts.app')
@section('title', __('ui.search'))
@section('content')
<div class="container-x py-10">
    <h1 class="text-3xl font-bold">{{ __('ui.search') }}</h1>
    <form class="mt-5 flex gap-2" action="{{ route('search') }}"><input name="q" value="{{ $q }}" class="input max-w-xl" placeholder="{{ __('ui.search_placeholder') }}" aria-label="{{ __('ui.search') }}"><button class="btn btn-primary">{{ __('ui.search') }}</button></form>
    @if($results === null)
        <p class="mt-6 text-slate-500">{{ __('ui.search_min') }}</p>
    @else
        @php $total = collect($results)->sum(fn ($r) => $r->count()); @endphp
        @if($total === 0)<div class="mt-6"><x-empty :text="__('ui.no_results')" /></div>@endif
        @foreach(['countries' => 'countries.show', 'cities' => 'cities.show', 'attractions' => 'attractions.show', 'hotels' => 'hotels.show', 'restaurants' => 'restaurants.show'] as $k => $route)
            @if($results[$k]->isNotEmpty())
                <h2 class="mb-3 mt-8 text-xl font-bold">{{ __('ui.nav_'.($k === 'cities' ? 'cities' : $k)) }}</h2>
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($results[$k] as $m)<a href="{{ route($route, $m) }}" class="card card-hover p-4 font-medium">{{ $m->tr('name') }}@if(isset($m->city) && $k !== 'cities')<span class="block text-xs text-slate-500">{{ $m->city->tr('name') }}</span>@endif</a>@endforeach
                </div>
            @endif
        @endforeach
    @endif
</div>
@endsection
