@extends('layouts.app')
@section('title', __('ui.nav_planner'))
@section('breadcrumbs') / <span>{{ __('ui.nav_planner') }}</span> @endsection
@php
    $preCity = null;
    if (!empty($prefill['city'])) $preCity = \App\Models\City::where('slug', $prefill['city'])->first()?->id;
    $preCountry = !empty($prefill['country']) ? $countries->firstWhere('slug', $prefill['country'])?->id : ($preCity ? \App\Models\City::find($preCity)->country_id : null);
    $cities = $countries->mapWithKeys(fn ($c) => [$c->id => $c->cities->map(fn ($x) => ['id' => $x->id, 'name' => $x->tr('name')])->values()]);
@endphp
@section('content')
<div class="container-x max-w-4xl py-8">
    <h1 class="text-3xl font-bold">🤖 {{ __('ui.planner_title') }}</h1>
    <p class="mt-1 text-slate-500">{{ __('ui.planner_sub') }}</p>
    <p class="mt-3 text-sm {{ $aiEnabled ? 'text-emerald-700' : 'text-amber-700 dark:text-amber-300' }}">{{ $aiEnabled ? '✅ '.__('ui.ai_enabled') : 'ℹ️ '.__('ui.ai_rule_based') }}</p>
    <form method="POST" action="{{ route('planner.store') }}" class="card mt-6 grid gap-5 p-6 sm:grid-cols-2" x-data="{ cities: @js($cities), country: '{{ old('country_id', $preCountry) }}', city: '{{ old('city_id', $preCity) }}' }">
        @csrf
        <x-field name="departure_city" :label="__('ui.departure_city')" :value="'Toshkent'" required class="input" />
        <div><label class="label" for="country_id">{{ __('ui.country') }} *</label><select id="country_id" x-model="country" @change="city = ''" class="input" required><option value="">—</option>@foreach($countries as $c)<option value="{{ $c->id }}">{{ $c->flag() }} {{ $c->tr('name') }}</option>@endforeach</select></div>
        <div><label class="label" for="city_id">{{ __('ui.city') }} *</label><select id="city_id" name="city_id" x-model="city" class="input" required><option value="">—</option><template x-for="c in (cities[country] || [])" :key="c.id"><option :value="c.id" x-text="c.name" :selected="c.id == city"></option></template></select>@error('city_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
        <div class="grid grid-cols-2 gap-3"><x-field name="start_date" type="date" :label="__('ui.start_date')" :value="now()->addDays(14)->toDateString()" required min="{{ now()->toDateString() }}" /><x-field name="end_date" type="date" :label="__('ui.end_date')" :value="now()->addDays(20)->toDateString()" required min="{{ now()->toDateString() }}" /></div>
        <div class="grid grid-cols-2 gap-3"><x-field name="travelers" type="number" min="1" max="12" :label="__('ui.travelers')" value="2" required /><x-field name="children" type="number" min="0" max="10" :label="__('ui.children')" value="0" /></div>
        <div class="grid grid-cols-2 gap-3"><x-field name="budget" type="number" min="0" step="any" :label="__('ui.total_budget')" value="1000" /><div><label class="label" for="currency">{{ __('ui.currency') }}</label><select id="currency" name="currency" class="input">@foreach($currencyService->all() as $c)<option value="{{ $c->code }}" @selected(old('currency', $currencyService->current()) === $c->code)>{{ $c->code }}</option>@endforeach</select></div></div>
        <div><label class="label" for="style">{{ __('ui.travel_style') }}</label><select id="style" name="style" class="input">@foreach($styles as $s)<option value="{{ $s }}" @selected(old('style', 'budget') === $s)>{{ __("ui.style_$s") }}</option>@endforeach</select></div>
        <fieldset class="sm:col-span-2"><legend class="label">{{ __('ui.interests') }}</legend><div class="flex flex-wrap gap-2">@foreach($interests as $i)<label class="chip cursor-pointer has-[:checked]:chip-active"><input type="checkbox" class="sr-only" name="interests[]" value="{{ $i }}" @checked(in_array($i, old('interests', ['history'])))>{{ __("ui.interest_$i") }}</label>@endforeach</div></fieldset>
        <div><label class="label" for="accommodation">{{ __('ui.accommodation') }}</label><select id="accommodation" name="accommodation" class="input"><option value="">{{ __('ui.any') }}</option>@foreach($hotelTypes as $h)<option value="{{ $h }}">{{ __("ui.cat_$h") }}</option>@endforeach</select></div>
        <div><label class="label" for="transport">{{ __('ui.transport_pref') }}</label><select id="transport" name="transport" class="input"><option value="">{{ __('ui.any') }}</option>@foreach(['taxi', 'public', 'mixed'] as $t)<option value="{{ $t }}">{{ __("ui.transport_$t") }}</option>@endforeach</select></div>
        <fieldset class="sm:col-span-2"><legend class="label">{{ __('ui.food_pref') }}</legend><div class="flex flex-wrap gap-2">@foreach(['uzbek', 'halal', 'local', 'international'] as $f)<label class="chip cursor-pointer has-[:checked]:chip-active"><input type="checkbox" class="sr-only" name="food[]" value="{{ $f }}" @checked(in_array($f, old('food', $f === 'uzbek' ? ['uzbek'] : [])))>{{ __("ui.food_$f") }}</label>@endforeach</div></fieldset>
        @if($errors->any())<ul class="list-disc pl-5 text-sm text-red-600 sm:col-span-2">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>@endif
        <div class="sm:col-span-2"><button class="btn btn-accent !px-8 !py-3">✨ {{ __('ui.generate_plan') }}</button></div>
    </form>
</div>
@endsection
