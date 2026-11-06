@extends('layouts.app')
@section('title', $guide->tr('title'))
@section('description', \Illuminate\Support\Str::limit(strip_tags((string) $guide->tr('excerpt')), 155))
@section('breadcrumbs') / <a href="{{ route('guides.index') }}" class="hover:text-ocean-600">{{ __('ui.nav_guides') }}</a> / <span>{{ $guide->tr('title') }}</span> @endsection
@section('content')
<article class="container-x max-w-3xl py-8"><x-cover :model="$guide" class="mb-6 h-56 rounded-2xl" emoji="📖" /><h1 class="text-3xl font-extrabold">{{ $guide->tr('title') }}</h1>
    <div class="prose-lite mt-5 text-lg text-slate-700 dark:text-slate-300">{!! nl2br(e($guide->tr('body'))) !!}</div></article>
@endsection
