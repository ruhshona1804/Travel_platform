@props(['item', 'kind'])
@php
    $emoji = ['attractions' => '🏛️', 'hotels' => '🏨', 'restaurants' => '🍽️'][$kind];
    $priceUsd = ['attractions' => $item->entrance_usd ?? null, 'hotels' => $item->price_usd ?? null, 'restaurants' => $item->avg_meal_usd ?? null][$kind];
    $suffix = ['attractions' => null, 'hotels' => __('ui.per_night'), 'restaurants' => __('ui.per_meal')][$kind];
    $cat = $item->category ?? $item->type ?? null;
@endphp
<a href="{{ route("$kind.show", $item) }}" class="card card-hover group block overflow-hidden">
    <x-cover :model="$item" class="h-44" :emoji="$emoji" />
    <div class="p-4">
        <div class="mb-1 flex items-start justify-between gap-2">
            <h3 class="font-bold leading-snug">{{ $item->tr('name') }}</h3>
            <x-stars :value="$item->rating" />
        </div>
        <div class="text-xs text-slate-500 dark:text-slate-400">{{ $item->city?->tr('name') }}@if($item->city?->country), {{ $item->city->country->tr('name') }}@endif</div>
        <div class="mt-2 flex flex-wrap items-center gap-1.5">
            @if($cat)<span class="badge badge-info">{{ __("ui.cat_$cat") }}</span>@endif
            @if($kind === 'restaurants' && $item->is_uzbek)<span class="badge badge-ok">🥘 {{ __('ui.uzbek') }}</span>@endif
            @if($kind === 'restaurants' && $item->is_halal)<span class="badge badge-ok">{{ __('ui.halal') }}</span>@endif
            @if($item->is_demo)<span class="badge badge-demo">{{ __('ui.demo') }}</span>@endif
        </div>
        <div class="mt-3 text-sm"><x-price :usd="$priceUsd" :verified="$item->price_verified" :suffix="$suffix" /></div>
    </div>
</a>
