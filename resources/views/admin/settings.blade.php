@extends('layouts.admin', ['heading' => __('admin.settings')])
@section('content')
<form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="grid gap-6 lg:grid-cols-2">@csrf @method('PUT')
    <section class="card space-y-4 p-6"><h2 class="font-bold">{{ __('admin.branding') }}</h2>
        <x-field name="app_name" :label="__('admin.app_name')" :value="setting('app_name', config('travel.name'))" required />
        <x-field name="tagline" :label="__('admin.tagline')" :value="setting('tagline')" />
        <div><label class="label" for="logo">{{ __('admin.logo') }}</label>@if($l = setting('logo'))<img src="{{ img($l) }}" class="mb-2 h-10" alt="">@endif<input id="logo" name="logo" type="file" accept="image/*" class="input"></div>
        <x-field name="contact_email" type="email" :label="__('admin.contact_email')" :value="setting('contact_email')" />
        <x-field name="footer_text" :label="__('admin.footer_text')" :value="setting('footer_text')" /></section>
    <section class="card space-y-4 p-6"><h2 class="font-bold">SEO & {{ __('admin.announcement') }}</h2>
        <div><label class="label" for="seo_description">{{ __('admin.seo_description') }}</label><textarea id="seo_description" name="seo_description" rows="3" class="input" maxlength="300">{{ old('seo_description', setting('seo_description')) }}</textarea></div>
        <x-field name="announcement" :label="__('admin.announcement')" :value="setting('announcement')" /></section>
    <section class="card p-6"><h2 class="mb-3 font-bold">{{ __('admin.languages') }}</h2><p class="mb-3 text-xs text-slate-500">{{ __('admin.uz_always') }}</p>
        <div class="flex flex-wrap gap-2">@php $on = array_keys(enabled_locales()); @endphp @foreach(config('travel.locales') as $c => $l)<label class="chip cursor-pointer has-[:checked]:chip-active"><input type="checkbox" class="sr-only" name="locales[]" value="{{ $c }}" @checked(in_array($c, $on)) @disabled($c === 'uz')>{{ $l['flag'] }} {{ $l['name'] }}</label>@endforeach</div></section>
    <section class="card p-6"><h2 class="mb-3 font-bold">{{ __('admin.home_sections') }}</h2>
        <div class="flex flex-wrap gap-2">@foreach($sections as $s)<label class="chip cursor-pointer has-[:checked]:chip-active"><input type="checkbox" class="sr-only" name="sections[]" value="{{ $s }}" @checked(section_visible($s))>{{ __("admin.section_$s") }}</label>@endforeach</div></section>
    <section class="card p-6 lg:col-span-2"><h2 class="mb-3 font-bold">{{ __('admin.api_config') }}</h2><p class="mb-3 text-xs text-slate-500">{{ __('admin.api_note') }}</p>
        <ul class="grid gap-2 text-sm sm:grid-cols-2">@foreach($apis as $k => $v)<li class="flex justify-between rounded-xl border border-slate-200 px-4 py-2 dark:border-white/10"><code>{{ $k }}</code>@if(is_bool($v))<span class="badge {{ $v ? 'badge-ok' : 'badge-demo' }}">{{ $v ? __('admin.configured') : __('admin.not_configured') }}</span>@else<span class="badge badge-info">{{ $v }}</span>@endif</li>@endforeach</ul></section>
    <div class="lg:col-span-2"><button class="btn btn-primary">{{ __('ui.save') }}</button></div>
</form>
@endsection
