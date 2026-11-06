@extends('layouts.app')
@section('title', __('ui.nav_budget'))
@section('breadcrumbs') / <span>{{ __('ui.nav_budget') }}</span> @endsection
@section('content')
@php $rates = $currencyService->all()->map->rate_per_usd; $labels = collect($categories)->mapWithKeys(fn ($c) => [$c => __("ui.bc_$c")]); @endphp
<div class="container-x py-8" x-data="budgetCalc(@js(['cats' => $categories, 'currency' => $currencyService->current(), 'rates' => $rates, 'labels' => $labels, 'checkUrl' => route('budget.check')]))">
    <h1 class="text-3xl font-bold">🧮 {{ __('ui.nav_budget') }}</h1>
    <p class="mt-1 text-slate-500">{{ __('ui.budget_sub') }}</p>

    <div class="mt-6 grid gap-6 lg:grid-cols-5">
        <form method="POST" action="{{ route('budget.store') }}" class="card space-y-4 p-6 lg:col-span-3">@csrf
            <div class="grid gap-3 sm:grid-cols-4">
                <div class="sm:col-span-2"><label class="label" for="title">{{ __('ui.title') }}</label><input id="title" name="title" class="input" required maxlength="120" value="{{ __('ui.my_trip') }}"></div>
                <div><label class="label" for="travelers">{{ __('ui.travelers') }}</label><input id="travelers" name="travelers" type="number" min="1" max="50" x-model.number="travelers" class="input"></div>
                <div><label class="label" for="days">{{ __('ui.days') }}</label><input id="days" name="days" type="number" min="1" max="365" x-model.number="days" class="input"></div>
                <div class="sm:col-span-2"><label class="label" for="country_id">{{ __('ui.country') }}</label><select id="country_id" name="country_id" class="input"><option value="">—</option>@foreach($countries as $c)<option value="{{ $c->id }}">{{ $c->tr('name') }}</option>@endforeach</select></div>
                <div><label class="label" for="currency">{{ __('ui.currency') }}</label><select id="currency" name="currency" x-model="currency" class="input">@foreach($currencyService->all() as $c)<option value="{{ $c->code }}">{{ $c->code }}</option>@endforeach</select></div>
            </div>
            <div class="grid gap-3 sm:grid-cols-2">@foreach($categories as $c)<div><label class="label" for="i_{{ $c }}">{{ __("ui.bc_$c") }}</label><input id="i_{{ $c }}" name="items[{{ $c }}]" type="number" min="0" step="any" x-model="amounts.{{ $c }}" class="input" placeholder="0"></div>@endforeach</div>
            <p class="text-xs text-slate-500">ℹ️ {{ __('ui.budget_estimate_note') }}</p>
            @auth<button class="btn btn-primary">{{ __('ui.save_budget') }}</button>@else<a class="btn btn-ghost" href="{{ route('login') }}">{{ __('ui.login_to_save') }}</a>@endauth
        </form>

        <div class="space-y-4 lg:col-span-2">
            <div class="card p-6"><div class="text-sm text-slate-500">{{ __('ui.total') }}</div><div class="text-3xl font-extrabold" x-text="fmt(total)"></div>
                <div class="mt-1 text-sm text-slate-500">≈ <span x-text="fmt(totalUsd, 'USD')"></span> · <span x-text="fmt(totalUzs, 'UZS')"></span></div>
                <dl class="mt-4 grid grid-cols-2 gap-3 text-sm"><div><dt class="text-slate-500">{{ __('ui.per_person') }}</dt><dd class="font-semibold" x-text="fmt(perPerson)"></dd></div><div><dt class="text-slate-500">{{ __('ui.per_day') }}</dt><dd class="font-semibold" x-text="fmt(perDay)"></dd></div></dl></div>
            <div class="card p-6"><canvas x-ref="chart" height="260" role="img" aria-label="{{ __('ui.budget_breakdown') }}"></canvas></div>
        </div>
    </div>

    <section class="card mt-8 p-6">
        <h2 class="text-xl font-bold">❓ {{ __('ui.can_i_travel') }}</h2>
        <div class="mt-4 grid gap-3 sm:grid-cols-4">
            <div class="sm:col-span-2"><label class="label" for="sc_country">{{ __('ui.country') }}</label><select id="sc_country" x-model="country" class="input"><option value="">—</option>@foreach($countries as $c)<option value="{{ $c->id }}">{{ $c->tr('name') }}</option>@endforeach</select></div>
            <div><label class="label" for="sc_budget">{{ __('ui.total_budget') }}</label><input id="sc_budget" type="number" min="1" x-model.number="budget" class="input"></div>
            <div class="flex items-end"><button type="button" class="btn btn-accent w-full" @click="check" :disabled="scLoading">{{ __('ui.check') }}</button></div>
        </div>
        <template x-if="scenario"><div class="mt-5">
            <p class="font-semibold" :class="scenario.enough ? 'text-emerald-600' : 'text-red-600'" x-text="scenario.enough ? '✅ {{ __('ui.budget_enough') }}' : '⚠️ {{ __('ui.budget_not_enough') }}'"></p>
            <div class="mt-3 grid gap-3 sm:grid-cols-3"><template x-for="(s, k) in scenario.scenarios" :key="k"><div class="rounded-xl border p-4" :class="scenario.fits.includes(k) ? 'border-emerald-400' : 'border-slate-200 dark:border-white/10'">
                <div class="font-semibold" x-text="({budget: '{{ __('ui.style_budget') }}', comfortable: '{{ __('ui.style_comfortable') }}', luxury: '{{ __('ui.style_luxury') }}'})[k]"></div>
                <div class="text-2xl font-extrabold" x-text="fmt(s.total * (rates[currency] || 1))"></div>
                <div class="text-xs text-slate-500"><span x-text="fmt(s.daily * (rates[currency] || 1))"></span> / {{ __('ui.per_person_day') }} <span x-show="!s.flights_known">· {{ __('ui.flights_not_included') }}</span></div></div></template></div>
            <p class="mt-3 text-xs text-slate-500">ℹ️ {{ __('ui.budget_estimate_note') }}</p></div></template>
    </section>
</div>
@endsection
