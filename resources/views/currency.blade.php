@extends('layouts.app')
@section('title', __('ui.nav_currency'))
@section('breadcrumbs') / <span>{{ __('ui.nav_currency') }}</span> @endsection
@section('content')
<div class="container-x max-w-3xl py-8">
    <h1 class="text-3xl font-bold">💱 {{ __('ui.nav_currency') }}</h1>
    <p class="mt-1 text-slate-500">{{ __('ui.currency_sub') }}</p>
    <div class="card mt-6 p-6" x-data="converter('{{ route('currency.convert') }}')">
        <div class="grid items-end gap-3 sm:grid-cols-[1fr_auto_1fr_auto]">
            <div><label class="label" for="amount">{{ __('ui.amount') }}</label><input id="amount" type="number" min="0" step="any" x-model="amount" @input.debounce.300ms="run" class="input"></div>
            <div><label class="label" for="from">{{ __('ui.from') }}</label><select id="from" x-model="from" @change="run" class="input">@foreach($currencies as $c)<option value="{{ $c->code }}">{{ $c->code }} — {{ $c->name }}</option>@endforeach</select></div>
            <button type="button" class="btn btn-ghost" @click="swap" aria-label="swap">⇄</button>
            <div><label class="label" for="to">{{ __('ui.to') }}</label><select id="to" x-model="to" @change="run" class="input">@foreach($currencies as $c)<option value="{{ $c->code }}">{{ $c->code }} — {{ $c->name }}</option>@endforeach</select></div>
        </div>
        <div class="mt-6 rounded-2xl bg-ocean-50 p-5 dark:bg-white/5"><div class="text-sm text-slate-500">{{ __('ui.result') }}</div><div class="mt-1 text-3xl font-extrabold text-ocean-700 dark:text-turq-300" x-text="result ?? '…'"></div><div class="mt-1 text-sm text-slate-500" x-show="rate">1 <span x-text="from"></span> = <span x-text="Number(rate).toLocaleString('en-US', {maximumFractionDigits: 6})"></span> <span x-text="to"></span></div></div>
    </div>
    <div class="mt-4 rounded-xl p-4 text-sm {{ $stale ? 'bg-amber-50 text-amber-900 dark:bg-amber-400/10 dark:text-amber-200' : 'bg-emerald-50 text-emerald-900 dark:bg-emerald-400/10 dark:text-emerald-200' }}">
        @if($stale) ⚠️ {{ __('ui.rates_stale') }} @else ✅ {{ __('ui.rates_live', ['time' => $updatedAt?->diffForHumans()]) }} @endif
    </div>
</div>
@endsection
