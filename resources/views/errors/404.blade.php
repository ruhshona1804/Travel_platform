@extends('layouts.app')
@section('title', '404')
@section('content')
<div class="container-x grid min-h-[60vh] place-items-center py-16 text-center"><div>
    <div class="text-7xl font-extrabold text-ocean-600 dark:text-turq-300">404</div>
    <h1 class="mt-3 text-2xl font-bold">{{ __('ui.err_title_404') }}</h1>
    <p class="mt-2 text-slate-500">{{ __('ui.err_text_404') }}</p>
    <a href="{{ route('home') }}" class="btn btn-primary mt-6">{{ __('ui.go_home') }}</a>
</div></div>
@endsection
