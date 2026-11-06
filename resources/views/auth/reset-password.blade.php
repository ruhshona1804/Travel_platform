@extends('layouts.app')
@section('title', __('ui.reset_password'))
@section('content')
<div class="container-x grid min-h-[60vh] place-items-center py-10"><div class="card w-full max-w-md p-8 shadow-xl">
    <h1 class="text-2xl font-extrabold">{{ __('ui.reset_password') }}</h1>
    <form method="POST" action="{{ route('password.update') }}" class="mt-5 space-y-4">@csrf<input type="hidden" name="token" value="{{ $token }}">
        <x-field name="email" type="email" :label="__('ui.email')" :value="$email" required />
        <x-field name="password" type="password" :label="__('ui.password')" required autocomplete="new-password" minlength="8" />
        <x-field name="password_confirmation" type="password" :label="__('ui.password_confirm')" required autocomplete="new-password" />
        <button class="btn btn-primary w-full">{{ __('ui.reset_password') }}</button></form>
</div></div>
@endsection
