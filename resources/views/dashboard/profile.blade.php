@extends('dashboard.layout', ['heading' => __('ui.profile')])
@section('body')
<form method="POST" action="{{ route('dashboard.profile.update') }}" enctype="multipart/form-data" class="card grid gap-4 p-6 sm:grid-cols-2">@csrf @method('PUT')
    <div class="flex items-center gap-4 sm:col-span-2"><img src="{{ auth()->user()->avatarUrl() }}" alt="" class="h-16 w-16 rounded-full object-cover"><div class="flex-1"><label class="label" for="avatar">{{ __('ui.profile_photo') }}</label><input id="avatar" name="avatar" type="file" accept="image/*" class="input">@error('avatar')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div></div>
    <x-field name="name" :label="__('ui.name')" :value="auth()->user()->name" required />
    <x-field name="email" type="email" :label="__('ui.email')" :value="auth()->user()->email" required />
    <div><label class="label" for="locale">{{ __('ui.language') }}</label><select id="locale" name="locale" class="input">@foreach(enabled_locales() as $c => $l)<option value="{{ $c }}" @selected((auth()->user()->locale ?? app()->getLocale()) === $c)>{{ $l['flag'] }} {{ $l['name'] }}</option>@endforeach</select></div>
    <div><label class="label" for="currency">{{ __('ui.currency') }}</label><select id="currency" name="currency" class="input">@foreach($currencies as $c)<option @selected(($currencyService->current()) === $c)>{{ $c }}</option>@endforeach</select></div>
    <div><label class="label" for="theme">{{ __('ui.theme') }}</label><select id="theme" name="theme" class="input">@foreach(['system', 'light', 'dark'] as $t)<option value="{{ $t }}" @selected((auth()->user()->theme ?? 'system') === $t)>{{ __("ui.theme_$t") }}</option>@endforeach</select></div>
    <div class="sm:col-span-2"><button class="btn btn-primary">{{ __('ui.save') }}</button></div>
</form>
<form method="POST" action="{{ route('dashboard.password.update') }}" class="card mt-6 grid gap-4 p-6 sm:grid-cols-3">@csrf @method('PUT')
    <h2 class="text-lg font-bold sm:col-span-3">{{ __('ui.change_password') }}</h2>
    <x-field name="current_password" type="password" :label="__('ui.current_password')" required autocomplete="current-password" />
    <x-field name="password" type="password" :label="__('ui.new_password')" required autocomplete="new-password" minlength="8" />
    <x-field name="password_confirmation" type="password" :label="__('ui.password_confirm')" required autocomplete="new-password" />
    <div class="sm:col-span-3"><button class="btn btn-primary">{{ __('ui.save') }}</button></div>
</form>
<div class="card mt-6 border-red-300/60 p-6"><h2 class="text-lg font-bold text-red-600">{{ __('ui.delete_account') }}</h2><p class="mt-1 text-sm text-slate-500">{{ __('ui.delete_account_warn') }}</p>
    <div class="mt-4"><x-confirm :action="route('dashboard.account.destroy')" :label="__('ui.delete_account')" :message="__('ui.delete_account_warn')" :password="true" /></div>@error('password')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror</div>
@endsection
