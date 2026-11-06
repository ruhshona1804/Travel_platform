@extends('layouts.app')
@section('title', __('ui.visa_for_uz'))
@section('breadcrumbs') / <span>{{ __('ui.nav_visa') }}</span> @endsection
@section('content')
<div class="container-x py-8" x-data="{ f: '' }">
    <h1 class="text-3xl font-bold">🛂 {{ __('ui.visa_for_uz') }}</h1>
    <p class="mt-1 text-slate-500">{{ __('ui.visa_sub') }}</p>
    <p class="mt-4 rounded-xl bg-amber-50 p-3 text-sm text-amber-900 dark:bg-amber-400/10 dark:text-amber-200">⚠️ {{ __('ui.visa_warning') }}</p>
    <div class="mt-5 flex flex-wrap gap-2"><button class="chip" :class="f === '' ? 'chip-active' : ''" @click="f = ''">{{ __('ui.all') }}</button>@foreach(\App\Models\VisaRequirement::STATUSES as $s)<button class="chip" :class="f === '{{ $s }}' ? 'chip-active' : ''" @click="f = '{{ $s }}'">{{ __("ui.vs_$s") }}</button>@endforeach</div>
    <div class="card mt-5 overflow-x-auto"><table class="table-x min-w-[640px]">
        <thead><tr><th>{{ __('ui.country') }}</th><th>{{ __('ui.visa') }}</th><th>{{ __('ui.max_stay') }}</th><th>{{ __('ui.verification') }}</th><th>{{ __('ui.last_verified') }}</th><th></th></tr></thead>
        <tbody>@foreach($countries as $c)@php $v = $c->visa; $st = $v->status ?? 'check_official'; @endphp
            <tr x-show="f === '' || f === '{{ $st }}'"><td class="font-semibold"><a href="{{ route('countries.show', $c) }}" class="hover:text-ocean-600">{{ $c->flag() }} {{ $c->tr('name') }}</a></td>
            <td><span class="badge {{ $st === 'visa_free' ? 'badge-ok' : ($st === 'visa_required' ? 'badge-warn' : 'badge-info') }}">{{ __("ui.vs_$st") }}</span></td>
            <td>{{ $v?->max_stay_days ? $v->max_stay_days.' '.__('ui.days_short') : '—' }}</td>
            <td><span class="badge {{ ($v->verification_status ?? '') === 'verified' ? 'badge-ok' : 'badge-demo' }}">{{ __('ui.verif_'.($v->verification_status ?? 'unverified')) }}</span></td>
            <td>{{ $v?->last_verified_at?->format('d.m.Y') ?? '—' }}</td>
            <td class="text-right">@if($v?->official_url)<a class="text-ocean-600 underline" href="{{ $v->official_url }}" target="_blank" rel="noopener noreferrer">{{ __('ui.official_source') }} ↗</a>@endif</td></tr>
        @endforeach</tbody></table></div>
</div>
@endsection
