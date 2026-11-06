@extends('layouts.app')
@php
    $name = $item->tr('name');
    $emoji = ['attractions' => '🏛️', 'hotels' => '🏨', 'restaurants' => '🍽️'][$kind];
    $type = ['attractions' => 'TouristAttraction', 'hotels' => 'Hotel', 'restaurants' => 'Restaurant'][$kind];
    $favType = ['attractions' => 'attraction', 'hotels' => 'hotel', 'restaurants' => 'restaurant'][$kind];
    $gallery = collect($item->gallery ?? [])->map(fn ($g) => img($g));
    if ($item->imageUrl()) $gallery->prepend($item->imageUrl());
    $avg = $item->reviews->avg('rating');
@endphp
@section('title', $name.' — '.$item->city->tr('name'))
@section('description', \Illuminate\Support\Str::limit(strip_tags((string) $item->tr('description')), 155))
@section('image', $item->imageUrl())
@section('breadcrumbs') / <a href="{{ route("$kind.index") }}" class="hover:text-ocean-600">{{ __("ui.nav_$kind") }}</a> / <a href="{{ route('cities.show', $item->city) }}" class="hover:text-ocean-600">{{ $item->city->tr('name') }}</a> / <span>{{ $name }}</span> @endsection
@push('head')
<script type="application/ld+json">{!! json_encode(array_filter(['@context' => 'https://schema.org', '@type' => $type, 'name' => $name, 'address' => $item->address, 'url' => url()->current(), 'geo' => $item->hasCoords() ? ['@type' => 'GeoCoordinates', 'latitude' => (float) $item->lat, 'longitude' => (float) $item->lng] : null]), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
@endpush
@section('content')
<div class="container-x py-6">
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="card overflow-hidden" x-data="{ i: 0, g: @js($gallery->values()) }">
                @if($gallery->isNotEmpty())
                    <div class="relative"><img :src="g[i]" alt="{{ $name }}" class="h-72 w-full object-cover sm:h-96">
                        <template x-if="g.length > 1"><div class="absolute inset-x-0 bottom-3 flex justify-center gap-2"><template x-for="(x, k) in g"><button @click="i = k" :class="i === k ? 'bg-white' : 'bg-white/50'" class="h-2.5 w-2.5 rounded-full" aria-label="Foto"></button></template></div></template></div>
                @else
                    <x-cover :model="$item" class="h-64 sm:h-80" :emoji="$emoji" />
                @endif
                <div class="p-6">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div><h1 class="text-3xl font-extrabold">{{ $name }}</h1>
                            <div class="mt-1 flex flex-wrap items-center gap-2 text-sm text-slate-500"><x-stars :value="$item->rating" /><span>{{ $item->city->tr('name') }}, {{ $item->city->country->tr('name') }}</span>
                                @if(isset($item->stars) && $item->stars)<span>{{ str_repeat('⭐', $item->stars) }}</span>@endif</div></div>
                        <x-fav :type="$favType" :model="$item" />
                    </div>
                    <div class="mt-3 flex flex-wrap gap-1.5">
                        @php $cat = $item->category ?? $item->type; @endphp
                        <span class="badge badge-info">{{ __("ui.cat_$cat") }}</span>
                        @if($kind === 'restaurants' && $item->is_uzbek)<span class="badge badge-ok">🥘 {{ __('ui.uzbek') }}</span>@endif
                        @if($kind === 'restaurants' && $item->is_halal)<span class="badge badge-ok">{{ __('ui.halal') }}</span>@endif
                        @if($item->is_demo)<span class="badge badge-demo">{{ __('ui.demo_data') }}</span>@endif
                    </div>
                    @if($item->tr('description'))<div class="prose-lite mt-4 text-slate-700 dark:text-slate-300">{!! nl2br(e($item->tr('description'))) !!}</div>@endif
                </div>
            </div>

            @if($item->hasCoords())
            <section class="card overflow-hidden p-2"><x-map :points="[$item->mapPoint()]" height="h-80" :zoom="15" />
                <div class="flex flex-wrap items-center justify-between gap-2 p-3 text-xs text-slate-500"><span>📍 {{ number_format((float) $item->lat, 5) }}, {{ number_format((float) $item->lng, 5) }}</span>
                <a class="font-semibold text-ocean-600 underline dark:text-turq-300" target="_blank" rel="noopener noreferrer" href="https://www.openstreetmap.org/directions?to={{ $item->lat }}%2C{{ $item->lng }}">{{ __('ui.get_directions') }} ↗</a></div></section>
            @else
                <section class="card p-6 text-sm text-slate-500">{{ __('ui.no_map_data') }}</section>
            @endif

            @if($nearby->isNotEmpty())
            <section class="card p-6"><h2 class="mb-3 text-xl font-bold">{{ __('ui.nearby') }}</h2>
                <ul class="divide-y divide-slate-100 dark:divide-white/10">@foreach($nearby as $n)<li><a href="{{ route($n['kind'].'.show', $n['model']) }}" class="flex items-center justify-between py-2.5 hover:text-ocean-600"><span>{{ ['attractions' => '🏛️', 'hotels' => '🏨', 'restaurants' => '🍽️'][$n['kind']] }} {{ $n['model']->tr('name') }}</span><span class="text-sm text-slate-500">{{ number_format($n['km'], 1) }} km</span></a></li>@endforeach</ul></section>
            @endif

            {{-- Sharhlar --}}
            <section class="card p-6" id="reviews">
                <h2 class="mb-1 text-xl font-bold">{{ __('ui.reviews') }} @if($avg)<span class="text-amber-500">★ {{ number_format($avg, 1) }}</span>@endif</h2>
                @forelse($item->reviews as $r)
                    <div class="border-b border-slate-100 py-3 last:border-0 dark:border-white/10"><div class="flex items-center gap-2 text-sm"><strong>{{ $r->user->name }}</strong><span class="text-amber-500">{{ str_repeat('★', $r->rating) }}</span><span class="text-xs text-slate-400">{{ $r->created_at->format('d.m.Y') }}</span></div>@if($r->body)<p class="mt-1 text-sm text-slate-700 dark:text-slate-300">{{ $r->body }}</p>@endif</div>
                @empty<p class="mt-2 text-sm text-slate-500">{{ __('ui.no_reviews') }}</p>@endforelse
                @auth
                <form method="POST" action="{{ route('reviews.store', [$favType, $item->id]) }}" class="mt-5 grid gap-3 border-t border-slate-100 pt-5 dark:border-white/10">@csrf
                    <div><label class="label" for="rating">{{ __('ui.rating') }}</label><select id="rating" name="rating" class="input !w-32">@foreach([5,4,3,2,1] as $s)<option value="{{ $s }}">{{ str_repeat('★', $s) }}</option>@endforeach</select></div>
                    <div><label class="label" for="body">{{ __('ui.your_review') }}</label><textarea id="body" name="body" rows="3" maxlength="1500" class="input"></textarea></div>
                    <div><button class="btn btn-primary">{{ __('ui.submit') }}</button></div>
                </form>
                @else<p class="mt-4 text-sm"><a class="text-ocean-600 underline" href="{{ route('login') }}">{{ __('ui.login_to_review') }}</a></p>@endauth
            </section>
        </div>

        <aside class="space-y-6">
            <section class="card p-6">
                <h2 class="mb-3 text-lg font-bold">{{ __('ui.details') }}</h2>
                <dl class="space-y-2.5 text-sm">
                    @if($item->address)<div class="flex justify-between gap-3"><dt class="text-slate-500">{{ __('ui.address') }}</dt><dd class="text-right font-medium">{{ $item->address }}</dd></div>@endif
                    @if($kind === 'attractions')
                        @if($item->opening_hours)<div class="flex justify-between gap-3"><dt class="text-slate-500">{{ __('ui.opening_hours') }}</dt><dd class="text-right font-medium">{{ $item->opening_hours }}</dd></div>@endif
                        <div class="flex justify-between gap-3"><dt class="text-slate-500">{{ __('ui.entrance') }}</dt><dd class="text-right"><x-price :usd="$item->entrance_usd" :verified="$item->price_verified" /></dd></div>
                        @if($item->duration_min)<div class="flex justify-between"><dt class="text-slate-500">{{ __('ui.duration') }}</dt><dd class="font-medium">~{{ $item->duration_min }} {{ __('ui.min') }}</dd></div>@endif
                        @if($item->transport_info)<div class="flex justify-between gap-3"><dt class="text-slate-500">{{ __('ui.transport') }}</dt><dd class="text-right font-medium">{{ $item->transport_info }}</dd></div>@endif
                    @elseif($kind === 'hotels')
                        <div class="flex justify-between gap-3"><dt class="text-slate-500">{{ __('ui.price') }}</dt><dd class="text-right"><x-price :usd="$item->price_usd" :verified="$item->price_verified" :suffix="__('ui.per_night')" /></dd></div>
                        @if($item->checkin)<div class="flex justify-between"><dt class="text-slate-500">{{ __('ui.checkin') }}</dt><dd class="font-medium">{{ $item->checkin }}</dd></div>@endif
                        @if($item->checkout)<div class="flex justify-between"><dt class="text-slate-500">{{ __('ui.checkout') }}</dt><dd class="font-medium">{{ $item->checkout }}</dd></div>@endif
                        @if($item->airport_km !== null || $airportKm)<div class="flex justify-between"><dt class="text-slate-500">{{ __('ui.airport_distance') }}</dt><dd class="font-medium">{{ $item->airport_km ?? number_format($airportKm, 0) }} km</dd></div>@endif
                    @else
                        @if($item->cuisine)<div class="flex justify-between gap-3"><dt class="text-slate-500">{{ __('ui.cuisine') }}</dt><dd class="font-medium">{{ $item->cuisine }}</dd></div>@endif
                        <div class="flex justify-between gap-3"><dt class="text-slate-500">{{ __('ui.avg_meal') }}</dt><dd class="text-right"><x-price :usd="$item->avg_meal_usd" :verified="$item->price_verified" /></dd></div>
                        @if($item->opening_hours)<div class="flex justify-between gap-3"><dt class="text-slate-500">{{ __('ui.opening_hours') }}</dt><dd class="text-right font-medium">{{ $item->opening_hours }}</dd></div>@endif
                        @if($item->phone)<div class="flex justify-between"><dt class="text-slate-500">{{ __('ui.phone') }}</dt><dd class="font-medium"><a href="tel:{{ preg_replace('/[^+\d]/', '', $item->phone) }}">{{ $item->phone }}</a></dd></div>@endif
                    @endif
                    @if($item->website)<div class="flex justify-between"><dt class="text-slate-500">{{ __('ui.website') }}</dt><dd><a class="font-medium text-ocean-600 underline dark:text-turq-300" href="{{ $item->website }}" target="_blank" rel="noopener noreferrer">{{ parse_url($item->website, PHP_URL_HOST) }} ↗</a></dd></div>@endif
                </dl>
                @if($kind === 'hotels')
                    @if($item->facilities)<div class="mt-4 flex flex-wrap gap-1.5">@foreach($item->facilities as $f)<span class="chip !py-0.5 text-xs">{{ $f }}</span>@endforeach</div>@endif
                    @if($item->booking_url)<a href="{{ $item->booking_url }}" target="_blank" rel="noopener noreferrer sponsored" class="btn btn-accent mt-5 w-full">{{ __('ui.book_external') }} ↗</a>@endif
                    <p class="mt-3 text-xs text-slate-500">ℹ️ {{ __('ui.no_availability') }}</p>
                @endif
                @unless($item->price_verified)<p class="mt-4 text-xs text-slate-500">ℹ️ {{ __('ui.estimate_hint') }}</p>@endunless
            </section>

            @if($transport->isNotEmpty())
            <section class="card p-6"><h2 class="mb-3 text-lg font-bold">{{ __('ui.transport') }}</h2><ul class="space-y-2 text-sm">@foreach($transport->take(4) as $t)<li class="flex justify-between gap-2"><span>{{ __('ui.tr_'.$t->type) }} · {{ $t->name }}</span>@if($t->website)<a class="text-ocean-600" href="{{ $t->website }}" target="_blank" rel="noopener noreferrer">↗</a>@endif</li>@endforeach</ul></section>
            @endif

            @if($similar->isNotEmpty())
            <section><h2 class="mb-3 text-lg font-bold">{{ __('ui.similar') }}</h2><div class="space-y-4">@foreach($similar as $s)<x-place-card :item="$s" :kind="$kind" />@endforeach</div></section>
            @endif
        </aside>
    </div>
</div>
@endsection
