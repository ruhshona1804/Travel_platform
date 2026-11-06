@extends('layouts.app')
@section('title', __('ui.forgot_password'))
@section('content')
<div class="container-x grid min-h-[60vh] place-items-center py-10"><div class="card w-full max-w-md p-8 shadow-xl">
    <h1 class="text-2xl font-extrabold">{{ __('ui.forgot_password') }}</h1><p class="mt-1 text-sm text-slate-500">{{ __('ui.forgot_sub') }}</p>
    @if(session('status'))<div class="mt-4 rounded-xl bg-emerald-50 p-3 text-sm text-emerald-800 dark:bg-emerald-400/10 dark:text-emerald-200">{{ session('status') }}</div>@endif
    <form method="POST" action="{{ route('password.email') }}" class="mt-5 space-y-4">@csrf<x-field name="email" type="email" :label="__('ui.email')" required autofocus /><button class="btn btn-primary w-full">{{ __('ui.send_reset_link') }}</button></form>
    <p class="mt-5 text-center text-sm"><a class="text-ocean-600 hover:underline" href="{{ route('login') }}">← {{ __('ui.login') }}</a></p>
</div></div>
@endsection
