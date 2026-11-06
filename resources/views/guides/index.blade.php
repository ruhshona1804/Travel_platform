@extends('layouts.app')
@section('title', __('ui.nav_guides'))
@section('breadcrumbs') / <span>{{ __('ui.nav_guides') }}</span> @endsection
@section('content')
<div class="container-x py-8"><h1 class="text-3xl font-bold">{{ __('ui.nav_guides') }}</h1>
    <div class="mt-6">@if($guides->isEmpty())<x-empty />@else<div class="grid gap-5 md:grid-cols-3">@foreach($guides as $g)<a href="{{ route('guides.show', $g) }}" class="card card-hover block overflow-hidden"><x-cover :model="$g" class="h-40" emoji="📖" /><div class="p-4"><h2 class="font-bold">{{ $g->tr('title') }}</h2><p class="mt-1 line-clamp-3 text-sm text-slate-500">{{ $g->tr('excerpt') }}</p></div></a>@endforeach</div><div class="mt-8">{{ $guides->links() }}</div>@endif</div></div>
@endsection
