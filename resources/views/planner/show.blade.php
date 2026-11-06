@extends('layouts.app')
@section('title', $plan->title)
@section('breadcrumbs') / <a href="{{ route('planner') }}" class="hover:text-ocean-600">{{ __('ui.nav_planner') }}</a> / <span>{{ $plan->title }}</span> @endsection
@section('content')
@php $s = $plan->summary; $cur = $currencyService; @endphp
<div class="container-x py-8" x-data="planMap(@js($points))">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div><h1 class="text-3xl font-extrabold">{{ $plan->title }}</h1>
            <p class="text-slate-500">{{ $plan->start_date?->format('d.m.Y') }} – {{ $plan->end_date?->format('d.m.Y') }} · {{ $s['people'] ?? '' }} {{ __('ui.people') }}</p>
            <span class="badge mt-2 {{ $plan->generated_by === 'ai' ? 'badge-ok' : 'badge-info' }}">{{ $plan->generated_by === 'ai' ? '🤖 '.__('ui.generated_ai') : '⚙️ '.__('ui.generated_rules') }}</span></div>
        <div class="flex gap-2">
            @guest<a class="btn btn-ghost btn-sm" href="{{ route('register') }}">{{ __('ui.register_to_save') }}</a>@endguest
            <a class="btn btn-ghost btn-sm" href="#" onclick="window.print();return false">🖨️ {{ __('ui.print') }}</a>
            <a class="btn btn-primary btn-sm" href="{{ route('planner') }}">{{ __('ui.new_plan') }}</a>
        </div>
    </div>

    @if(!empty($s['narrative']))<p class="card mt-5 p-5 text-slate-700 dark:text-slate-300">{{ $s['narrative'] }}</p>@endif

    <div class="mt-6 grid gap-4 sm:grid-cols-3">
        <div class="card p-5"><div class="text-sm text-slate-500">{{ __('ui.estimated_total') }}</div><div class="mt-1 text-2xl font-extrabold">{{ price((float) $plan->total_usd) }}</div>
            @if(($s['budget_usd'] ?? null) !== null)<div class="mt-1 text-sm {{ $s['over_budget'] ? 'text-red-600' : 'text-emerald-600' }}">{{ $s['over_budget'] ? '⚠️ '.__('ui.over_budget') : '✅ '.__('ui.within_budget') }} ({{ price((float) $s['budget_usd']) }})</div>@endif</div>
        <div class="card p-5"><div class="text-sm text-slate-500">{{ __('ui.per_person') }}</div><div class="mt-1 text-2xl font-extrabold">{{ price($plan->total_usd / max(1, $s['people'] ?? 1)) }}</div></div>
        <div class="card p-5"><div class="text-sm text-slate-500">{{ __('ui.per_day') }}</div><div class="mt-1 text-2xl font-extrabold">{{ price($plan->total_usd / max(1, $s['days'] ?? 1)) }}</div></div>
    </div>

    <div class="mt-6 grid gap-4 md:grid-cols-2">
        <div class="card p-5"><h2 class="mb-2 font-bold">✈️ {{ __('ui.flights') }}</h2>
            @if(!empty($s['flight']))<p class="text-sm">{{ $s['flight']['airline'] }} · {{ $s['flight']['from'] }} → {{ $plan->city->tr('name') }}</p><p class="text-sm"><x-price :usd="$s['flight']['price_usd']" :verified="false" /> <span class="badge badge-demo">{{ __('ui.sample') }}</span></p>
            @else<p class="text-sm text-slate-500">{{ __('ui.no_flights') }}</p>@endif
            <a class="mt-2 inline-block text-sm text-ocean-600" href="{{ route('flights.compare', ['city' => $plan->city_id]) }}">{{ __('ui.compare_flights') }} →</a></div>
        <div class="card p-5"><h2 class="mb-2 font-bold">🏨 {{ __('ui.accommodation') }}</h2>
            @if(!empty($s['hotel']))<p class="text-sm"><a class="font-semibold text-ocean-600 hover:underline" href="{{ route('hotels.show', $s['hotel']['slug']) }}">{{ $s['hotel']['name'] }}</a></p><p class="text-sm">{{ $s['hotel']['nights'] }} {{ __('ui.nights') }} × {{ $s['hotel']['rooms'] }} {{ __('ui.rooms') }} · <x-price :usd="$s['hotel']['price_usd']" :verified="false" /></p><p class="mt-1 text-xs text-slate-500">{{ __('ui.no_availability') }}</p>
            @else<p class="text-sm text-slate-500">{{ __('ui.empty') }}</p>@endif</div>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-5">
        <div class="space-y-5 lg:col-span-3">
            @foreach($plan->days as $i => $d)
            <section class="card p-5" @mouseenter="pick({{ $i }})">
                <div class="mb-3 flex items-center justify-between"><h2 class="text-lg font-bold"><span class="mr-2 grid-cols-1 rounded-lg bg-ocean-600 px-2 py-0.5 text-sm text-white">{{ $d->day_number }}</span>{{ $d->title }}</h2><div class="text-sm font-semibold">{{ price((float) $d->cost_usd) }}</div></div>
                <ol class="space-y-2">@foreach($d->items as $it)
                    <li class="flex items-start gap-3 text-sm"><span class="mt-0.5">{{ ['attraction' => '📍', 'restaurant' => '🍽️', 'hotel' => '🏨', 'transport' => '🚕', 'note' => '📝'][$it->kind] ?? '•' }}</span>
                        <div class="flex-1"><div class="font-medium">{{ $it->title }}</div>@if($it->note)<div class="text-xs text-slate-500">{{ $it->note }}</div>@endif</div>
                        @if($it->cost_usd > 0)<div class="text-right"><x-price :usd="$it->cost_usd" :verified="! $it->cost_estimated" /></div>@endif</li>
                @endforeach</ol>
            </section>
            @endforeach
        </div>
        <aside class="lg:col-span-2"><div class="sticky top-20 space-y-3">
            <div class="flex flex-wrap gap-1.5">@foreach($plan->days as $i => $d)<button class="chip" :class="day === {{ $i }} ? 'chip-active' : ''" @click="pick({{ $i }})">{{ __('ui.plan_day', ['n' => $d->day_number]) }}</button>@endforeach</div>
            <div class="card overflow-hidden p-2"><div x-ref="map" class="h-96 w-full" data-tiles="{{ config('travel.map.tiles') }}" data-attribution="{{ config('travel.map.attribution') }}"></div></div>
        </div></aside>
    </div>

    @if($plan->tips)<section class="card mt-8 p-6"><h2 class="mb-3 text-lg font-bold">💡 {{ __('ui.travel_tips_title') }}</h2><ul class="list-disc space-y-1 pl-5 text-sm text-slate-700 dark:text-slate-300">@foreach($plan->tips as $t)<li>{{ $t }}</li>@endforeach</ul></section>@endif
    <p class="mt-6 text-xs text-slate-500">⚠️ {{ __('ui.plan_disclaimer') }}</p>
</div>
@endsection
