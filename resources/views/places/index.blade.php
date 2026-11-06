@extends('layouts.app')
@section('title', __("ui.nav_$kind"))
@section('breadcrumbs') / <span>{{ __("ui.nav_$kind") }}</span> @endsection
@section('content')
<div class="container-x py-8" x-data="{ view: 'grid' }">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div><h1 class="text-3xl font-bold">{{ __("ui.nav_$kind") }}</h1><p class="text-slate-500">{{ __("ui.{$kind}_sub") }}</p></div>
        <div class="flex gap-2"><button @click="view = 'grid'" :class="view === 'grid' ? 'chip-active' : ''" class="chip">▦ {{ __('ui.list') }}</button><button @click="view = 'map'" :class="view === 'map' ? 'chip-active' : ''" class="chip">🗺️ {{ __('ui.nav_map') }}</button></div>
    </div>
    <form method="GET" class="card mt-5 grid gap-3 p-4 sm:grid-cols-2 lg:grid-cols-7">
        <div class="lg:col-span-2"><label class="label" for="q">{{ __('ui.search') }}</label><input id="q" name="q" value="{{ request('q') }}" class="input"></div>
        <div><label class="label" for="country">{{ __('ui.country') }}</label><select id="country" name="country" class="input"><option value="">{{ __('ui.any') }}</option>@foreach($countries as $c)<option value="{{ $c->slug }}" @selected(request('country') === $c->slug)>{{ $c->tr('name') }}</option>@endforeach</select></div>
        <div><label class="label" for="city">{{ __('ui.city') }}</label><select id="city" name="city" class="input"><option value="">{{ __('ui.any') }}</option>@foreach($cities as $c)<option value="{{ $c->slug }}" @selected(request('city') === $c->slug)>{{ $c->tr('name') }}</option>@endforeach</select></div>
        <div><label class="label" for="category">{{ __('ui.category') }}</label><select id="category" name="category" class="input"><option value="">{{ __('ui.any') }}</option>@foreach($categories as $c)<option value="{{ $c }}" @selected(request('category') === $c)>{{ __("ui.cat_$c") }}</option>@endforeach</select></div>
        <div><label class="label" for="max_price">{{ __('ui.max_price') }} (USD)</label><input id="max_price" type="number" min="0" name="max_price" value="{{ request('max_price') }}" class="input"></div>
        <div><label class="label" for="sort">{{ __('ui.sort') }}</label><select id="sort" name="sort" class="input">@foreach(['popularity' => 'sort_popularity', 'rating' => 'sort_rating', 'price' => 'sort_price'] as $v => $l)<option value="{{ $v }}" @selected(request('sort', 'popularity') === $v)>{{ __("ui.$l") }}</option>@endforeach</select></div>
        <div class="flex items-end gap-2 sm:col-span-2 lg:col-span-7"><button class="btn btn-primary">{{ __('ui.filter') }}</button><a href="{{ route("$kind.index") }}" class="btn btn-ghost">{{ __('ui.reset') }}</a>
            <label class="ml-auto flex items-center gap-2 text-sm">{{ __('ui.min_rating') }}<select name="min_rating" class="input !w-24"><option value="">—</option>@foreach([3, 3.5, 4, 4.5] as $r)<option value="{{ $r }}" @selected(request('min_rating') == $r)>{{ $r }}+</option>@endforeach</select></label></div>
    </form>
    <div class="mt-6" x-show="view === 'grid'">
        @if($items->isEmpty())<x-empty :text="__('ui.no_results')" />@else
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">@foreach($items as $i)<x-place-card :item="$i" :kind="$kind" />@endforeach</div>
            <div class="mt-8">{{ $items->links() }}</div>
        @endif
    </div>
    <div class="card mt-6 overflow-hidden p-2" x-show="view === 'map'" x-cloak><x-map :points="$points" height="h-[32rem]" /></div>
</div>
@endsection
