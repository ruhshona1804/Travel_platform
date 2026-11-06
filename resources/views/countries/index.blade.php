@extends('layouts.app')
@section('title', __('ui.nav_countries'))
@section('description', __('ui.countries_sub'))
@section('breadcrumbs') / <span>{{ __('ui.nav_countries') }}</span> @endsection
@section('content')
<div class="container-x py-8">
    <h1 class="text-3xl font-bold">{{ __('ui.nav_countries') }}</h1>
    <p class="mt-1 text-slate-500">{{ __('ui.countries_sub') }}</p>
    <form method="GET" class="card mt-6 grid gap-3 p-4 sm:grid-cols-2 lg:grid-cols-6">
        <div class="lg:col-span-2"><label class="label" for="q">{{ __('ui.country_or_city') }}</label><input id="q" name="q" value="{{ request('q') }}" class="input"></div>
        @php $sel = fn ($n, $opts, $pre) => [$n, $opts, $pre]; @endphp
        <div><label class="label" for="continent">{{ __('ui.continent') }}</label><select id="continent" name="continent" class="input"><option value="">{{ __('ui.any') }}</option>@foreach(\App\Models\Country::CONTINENTS as $c)<option value="{{ $c }}" @selected(request('continent') === $c)>{{ __("ui.continent_$c") }}</option>@endforeach</select></div>
        <div><label class="label" for="budget">{{ __('ui.budget') }}</label><select id="budget" name="budget" class="input"><option value="">{{ __('ui.any') }}</option>@foreach(['low', 'mid', 'high'] as $b)<option value="{{ $b }}" @selected(request('budget') === $b)>{{ __("ui.budget_$b") }}</option>@endforeach</select></div>
        <div><label class="label" for="type">{{ __('ui.travel_type') }}</label><select id="type" name="type" class="input"><option value="">{{ __('ui.any') }}</option>@foreach(\App\Models\Country::TRAVEL_TYPES as $t)<option value="{{ $t }}" @selected(request('type') === $t)>{{ __("ui.style_$t") }}</option>@endforeach</select></div>
        <div><label class="label" for="season">{{ __('ui.season') }}</label><select id="season" name="season" class="input"><option value="">{{ __('ui.any') }}</option>@foreach(['spring', 'summer', 'autumn', 'winter'] as $s)<option value="{{ $s }}" @selected(request('season') === $s)>{{ __("ui.season_$s") }}</option>@endforeach</select></div>
        <div><label class="label" for="visa">{{ __('ui.visa') }}</label><select id="visa" name="visa" class="input"><option value="">{{ __('ui.any') }}</option>@foreach(['visa_free', 'e_visa', 'on_arrival', 'visa_required', 'check_official'] as $v)<option value="{{ $v }}" @selected(request('visa') === $v)>{{ __("ui.vs_$v") }}</option>@endforeach</select></div>
        <div class="flex items-end gap-2 lg:col-span-2"><button class="btn btn-primary flex-1">{{ __('ui.filter') }}</button><a href="{{ route('countries.index') }}" class="btn btn-ghost">{{ __('ui.reset') }}</a></div>
    </form>
    <div class="mt-6">
        @if($countries->isEmpty())<x-empty :text="__('ui.no_results')" />@else
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">@foreach($countries as $c)<x-country-card :country="$c" />@endforeach</div>
            <div class="mt-8">{{ $countries->links() }}</div>
        @endif
    </div>
</div>
@endsection
