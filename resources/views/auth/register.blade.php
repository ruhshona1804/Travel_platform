@extends('layouts.app')
@section('title', __('ui.register'))
@section('content')
<div class="container-x grid min-h-[70vh] place-items-center py-10"><div class="card w-full max-w-md p-8 shadow-xl">
    <div class="mb-6 text-center"><div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-gradient-to-br from-ocean-600 to-turq-500 text-2xl text-white">🧭</div><h1 class="mt-4 text-2xl font-extrabold">{{ __('ui.register') }}</h1><p class="mt-1 text-sm text-slate-500">{{ __('ui.register_sub') }}</p></div>
    <form method="POST" action="{{ route('register') }}" class="space-y-4">@csrf
        <x-field name="name" :label="__('ui.name')" required autocomplete="name" autofocus />
        <x-field name="email" type="email" :label="__('ui.email')" required autocomplete="email" />
        <x-field name="password" type="password" :label="__('ui.password')" required autocomplete="new-password" minlength="8" />
        <x-field name="password_confirmation" type="password" :label="__('ui.password_confirm')" required autocomplete="new-password" />
        <button class="btn btn-accent w-full !py-3">{{ __('ui.register') }}</button>
    </form>
    @if(config('travel.google.enabled'))<a href="{{ route('google.redirect') }}" class="btn btn-ghost mt-3 w-full">G &nbsp;{{ __('ui.google_login') }}</a>@endif
    <p class="mt-6 text-center text-sm text-slate-500">{{ __('ui.have_account') }} <a class="font-semibold text-ocean-600 hover:underline" href="{{ route('login') }}">{{ __('ui.login') }}</a></p>
</div></div>
@endsection
