<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="csrf-token" content="{{ csrf_token() }}"><meta name="robots" content="noindex,nofollow">
<title>{{ $heading ?? __('admin.panel') }} · {{ app_name() }}</title>
<script>(function(){try{var t=localStorage.getItem('theme');if(t==='dark'||(t!=='light'&&matchMedia('(prefers-color-scheme: dark)').matches))document.documentElement.classList.add('dark')}catch(e){}})();</script>
@vite(['resources/css/app.css', 'resources/js/app.js'])</head>
<body x-data="{ side: false }" x-init="$store.theme.init()" data-auth="1" class="min-h-screen">
@php
    $groups = [
        'admin.overview' => [['admin.index', '📊', 'admin.dashboard']],
        'admin.content' => [['admin.resource.index', '🌍', 'admin.countries', 'countries'], ['admin.resource.index', '🏙️', 'admin.cities', 'cities'], ['admin.resource.index', '📍', 'admin.attractions', 'attractions'], ['admin.resource.index', '🏨', 'admin.hotels', 'hotels'], ['admin.resource.index', '🍽️', 'admin.restaurants', 'restaurants'], ['admin.resource.index', '🛬', 'admin.airports', 'airports'], ['admin.resource.index', '✈️', 'admin.airlines', 'airlines'], ['admin.resource.index', '🛫', 'admin.flights', 'flights'], ['admin.resource.index', '🛂', 'admin.visas', 'visas'], ['admin.resource.index', '🚕', 'admin.transport', 'transport'], ['admin.resource.index', '📖', 'admin.guides', 'guides']],
    ];
    $sys = auth()->user()->isAdmin() ? [['admin.users.index', '👥', 'admin.users'], ['admin.currencies.index', '💱', 'admin.currencies'], ['admin.settings', '⚙️', 'admin.settings']] : [];
@endphp
<div class="flex min-h-screen">
    <aside :class="side ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-40 w-64 -translate-x-full overflow-y-auto bg-ocean-900 p-4 text-slate-300 transition lg:static lg:translate-x-0">
        <a href="{{ route('admin.index') }}" class="mb-5 flex items-center gap-2 text-lg font-extrabold text-white">🧭 {{ app_name() }}</a>
        @foreach($groups as $title => $links)<div class="mb-1 mt-4 px-2 text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __($title) }}</div>
            @foreach($links as $l)@php $active = $l[3] ?? null ? (request()->route('resource') === $l[3]) : request()->routeIs($l[0]); @endphp
                <a href="{{ isset($l[3]) ? route($l[0], $l[3]) : route($l[0]) }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm hover:bg-white/10 {{ $active ? 'bg-white/10 text-white' : '' }}">{{ $l[1] }} {{ __($l[2]) }}</a>@endforeach @endforeach
        @if($sys)<div class="mb-1 mt-4 px-2 text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('admin.system') }}</div>@foreach($sys as $l)<a href="{{ route($l[0]) }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm hover:bg-white/10 {{ request()->routeIs($l[0].'*') ? 'bg-white/10 text-white' : '' }}">{{ $l[1] }} {{ __($l[2]) }}</a>@endforeach @endif
        <a href="{{ route('home') }}" class="mt-6 flex items-center gap-2 rounded-lg px-3 py-2 text-sm hover:bg-white/10">↩ {{ __('admin.back_to_site') }}</a>
    </aside>
    <div class="min-w-0 flex-1">
        <header class="sticky top-0 z-30 flex items-center gap-3 border-b border-slate-200 bg-white/80 px-4 py-3 backdrop-blur dark:border-white/10 dark:bg-night/80">
            <button class="btn btn-ghost btn-sm lg:hidden" @click="side = !side" aria-label="{{ __('ui.menu') }}">☰</button>
            <div class="font-semibold">{{ $heading ?? '' }}</div>
            <div class="ml-auto flex items-center gap-2">
                <button class="btn btn-ghost btn-sm" @click="$store.theme.toggle()" aria-label="{{ __('ui.toggle_theme') }}">🌓</button>
                <span class="hidden text-sm text-slate-500 sm:inline">{{ auth()->user()->name }} · {{ __('admin.role_'.auth()->user()->role) }}</span>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-ghost btn-sm">{{ __('ui.logout') }}</button></form>
            </div>
        </header>
        @include('partials.flash')
        <main class="p-4 sm:p-6">@yield('content')</main>
    </div>
</div>
</body></html>
