@extends('layouts.app')
@section('title', $heading)
@section('content')
<div class="container-x grid gap-6 py-8 lg:grid-cols-4">
    <div>@include('dashboard.nav')</div>
    <div class="lg:col-span-3">
        @if(! auth()->user()->hasVerifiedEmail())<div class="mb-4 flex flex-wrap items-center justify-between gap-2 rounded-xl bg-amber-50 p-3 text-sm text-amber-900 dark:bg-amber-400/10 dark:text-amber-200"><span>✉️ {{ __('ui.verify_banner') }}</span><form method="POST" action="{{ route('verification.send') }}">@csrf<button class="font-semibold underline">{{ __('ui.resend_verification') }}</button></form></div>@endif
        <h1 class="mb-5 text-2xl font-bold">{{ $heading }}</h1>
        @yield('body')
    </div>
</div>
@endsection
