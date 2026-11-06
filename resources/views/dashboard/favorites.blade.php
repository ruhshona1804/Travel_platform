@extends('dashboard.layout', ['heading' => __('ui.favorites')])
@section('body')
@if($groups->isEmpty())<x-empty :text="__('ui.no_favorites')" />@endif
@foreach(['country' => 'nav_countries', 'attraction' => 'nav_attractions', 'hotel' => 'nav_hotels', 'restaurant' => 'nav_restaurants'] as $k => $l)
    @if($groups->has($k))<h2 class="mb-3 mt-6 text-lg font-bold">{{ __("ui.$l") }}</h2>
    <div class="grid gap-4 sm:grid-cols-2">@foreach($groups[$k] as $f)@php $m = $f->favoritable; @endphp
        <div class="card flex items-center justify-between gap-3 p-4"><a class="font-semibold hover:text-ocean-600" href="{{ route($k === 'country' ? 'countries.show' : $k.'s.show', $m) }}">{{ $k === 'country' ? $m->flag().' ' : '' }}{{ $m->tr('name') }}</a>
            <form method="POST" action="{{ route('favorites.toggle', [$k, $m->id]) }}">@csrf<button class="text-red-500" aria-label="{{ __('ui.remove_favorite') }}">✕</button></form></div>@endforeach</div>@endif
@endforeach
@endsection
