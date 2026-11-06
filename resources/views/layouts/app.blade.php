<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $dec = fn ($v) => html_entity_decode(trim($v), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $metaTitle = $dec($__env->yieldContent('title')) ? $dec($__env->yieldContent('title')).' · '.app_name() : app_name().' — '.__('ui.tagline');
        $metaDesc = $dec($__env->yieldContent('description')) ?: (setting('seo_description') ?: __('ui.hero_sub'));
        $metaImg = trim($__env->yieldContent('image'));
    @endphp
    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDesc }}">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDesc }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    @if($metaImg)<meta property="og:image" content="{{ $metaImg }}">@endif
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🧭</text></svg>">
    <script>
        (function () {
            try {
                var t = localStorage.getItem('theme') || @json(auth()->user()?->theme ?? 'system');
                if (t === 'dark' || (t !== 'light' && matchMedia('(prefers-color-scheme: dark)').matches)) document.documentElement.classList.add('dark');
            } catch (e) {}
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body data-auth="{{ auth()->check() ? 1 : 0 }}" x-data x-init="$store.theme.init()" class="min-h-screen flex flex-col">
    <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:z-50 focus:bg-white focus:p-3">{{ __('ui.skip_to_content') }}</a>

    @if($a = setting('announcement'))
        <div class="bg-coral-500 px-4 py-2 text-center text-sm font-medium text-white">{{ $a }}</div>
    @endif

    @include('partials.header')

    @include('partials.flash')

    <main id="main" class="flex-1">
        @hasSection('breadcrumbs')
            <nav aria-label="breadcrumb" class="container-x pt-5 text-sm text-slate-500 dark:text-slate-400">
                <a href="{{ route('home') }}" class="hover:text-ocean-600">{{ __('ui.nav_home') }}</a>
                @yield('breadcrumbs')
            </nav>
        @endif
        @yield('content')
    </main>

    @include('partials.footer')
    @include('partials.assistant')
    @stack('scripts')
</body>
</html>
