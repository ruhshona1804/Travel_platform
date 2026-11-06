@extends('layouts.app')
@section('title', __('ui.login'))
@section('content')
<div class="container-x grid min-h-[70vh] place-items-center py-10"><div class="card w-full max-w-md p-8 shadow-xl">
    <div class="mb-6 text-center"><div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-gradient-to-br from-ocean-600 to-turq-500 text-2xl text-white">🧭</div><h1 class="mt-4 text-2xl font-extrabold">{{ __('ui.login') }}</h1><p class="mt-1 text-sm text-slate-500">{{ __('ui.login_sub') }}</p></div>
    @if(session('status'))<div class="mb-4 rounded-xl bg-emerald-50 p-3 text-sm text-emerald-800 dark:bg-emerald-400/10 dark:text-emerald-200">{{ session('status') }}</div>@endif
    <form method="POST" action="{{ route('login') }}" class="space-y-4">@csrf
        <x-field name="email" type="email" :label="__('ui.email')" required autocomplete="email" autofocus />
        <x-field name="password" type="password" :label="__('ui.password')" required autocomplete="current-password" />
        <div class="flex items-center justify-between text-sm"><label class="flex items-center gap-2"><input type="checkbox" name="remember" value="1"> {{ __('ui.remember_me') }}</label><a class="text-ocean-600 hover:underline" href="{{ route('password.request') }}">{{ __('ui.forgot_password') }}</a></div>
        <button class="btn btn-primary w-full !py-3">{{ __('ui.login') }}</button>
    </form>
    @if(config('travel.google.enabled'))<a href="{{ route('google.redirect') }}" class="btn btn-ghost mt-3 w-full">G &nbsp;{{ __('ui.google_login') }}</a>@endif
    <p class="mt-6 text-center text-sm text-slate-500">{{ __('ui.no_account') }} <a class="font-semibold text-ocean-600 hover:underline" href="{{ route('register') }}">{{ __('ui.register') }}</a></p>
</div></div>
@endsection
