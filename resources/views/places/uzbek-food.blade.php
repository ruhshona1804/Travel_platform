@extends('layouts.app')
@section('title', __('ui.uzbek_food_title'))
@section('description', __('ui.uzbek_food_sub'))
@section('breadcrumbs') / <span>{{ __('ui.nav_uzbek_food') }}</span> @endsection
@section('content')
<div class="container-x py-8">
    <div class="hero-bg rounded-3xl p-8 text-white sm:p-10"><h1 class="text-3xl font-extrabold sm:text-4xl">🥘 {{ __('ui.uzbek_food_title') }}</h1><p class="mt-2 max-w-2xl text-ocean-100">{{ __('ui.uzbek_food_sub') }}</p></div>
    <div class="mt-5 flex flex-wrap gap-2"><a href="{{ route('uzbek-food') }}" class="chip {{ request('country') ? '' : 'chip-active' }}">{{ __('ui.all') }}</a>@foreach($countries as $c)<a href="{{ route('uzbek-food', ['country' => $c->slug]) }}" class="chip {{ request('country') === $c->slug ? 'chip-active' : '' }}">{{ $c->flag() }} {{ $c->tr('name') }}</a>@endforeach</div>
    <div class="mt-6">@if($items->isEmpty())<x-empty :text="__('ui.no_results')" />@else<div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">@foreach($items as $i)<x-place-card :item="$i" kind="restaurants" />@endforeach</div>@endif</div>
    <div class="card mt-8 overflow-hidden p-2"><x-map :points="$points" height="h-96" /></div>
</div>
@endsection
