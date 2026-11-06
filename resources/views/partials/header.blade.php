@php
    $links = [
        ['countries.index', 'nav_countries'], ['attractions.index', 'nav_attractions'], ['hotels.index', 'nav_hotels'],
        ['restaurants.index', 'nav_restaurants'], ['airlines.index', 'nav_airlines'], ['visa.index', 'nav_visa'],
        ['map', 'nav_map'], ['planner', 'nav_planner'],
    ];
    $locales = enabled_locales();
    $cur = $currencyService->current();
@endphp
<header class="sticky top-0 z-40 border-b border-slate-200/60 bg-white/80 backdrop-blur-xl dark:border-white/10 dark:bg-night/80" x-data="{ menu: false }">
    <div class="container-x flex h-16 items-center gap-3">
        <a href="{{ route('home') }}" class="flex items-center gap-2 text-lg font-extrabold text-ocean-700 dark:text-white">
            @if($logo = setting('logo'))
                <img src="{{ img($logo) }}" alt="{{ app_name() }}" class="h-8 w-auto">
            @else
                <span class="grid h-9 w-9 place-items-center rounded-xl bg-gradient-to-br from-ocean-600 to-turq-500 text-lg text-white shadow">🧭</span>
            @endif
            <span class="hidden sm:inline">{{ app_name() }}</span>
        </a>

        <nav class="ml-4 hidden items-center gap-0.5 2xl:flex" aria-label="{{ __('ui.main_nav') }}">
            @foreach($links as [$route, $key])
                <a href="{{ route($route) }}" class="nav-link whitespace-nowrap {{ request()->routeIs(str_replace('.index', '*', $route)) ? 'nav-link-active' : '' }}">{{ __("ui.$key") }}</a>
            @endforeach
        </nav>

        <div class="ml-auto flex items-center gap-2">
            {{-- Til --}}
            <div class="relative" x-data="{ o: false }" @click.outside="o = false">
                <button class="btn btn-ghost btn-sm" @click="o = !o" aria-label="{{ __('ui.language') }}" :aria-expanded="o">
                    {{ $locales[app()->getLocale()]['flag'] ?? '' }} <span class="uppercase">{{ app()->getLocale() }}</span>
                </button>
                <div x-show="o" x-cloak x-transition class="card absolute right-0 mt-2 w-48 overflow-hidden p-1">
                    @foreach($locales as $code => $l)
                        <form method="POST" action="{{ route('locale', $code) }}">@csrf
                            <button class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-sm hover:bg-slate-100 dark:hover:bg-white/10 {{ app()->getLocale() === $code ? 'font-bold text-turq-600' : '' }}">
                                <span>{{ $l['flag'] }}</span> {{ $l['name'] }}
                            </button>
                        </form>
                    @endforeach
                </div>
            </div>
            {{-- Valyuta --}}
            <div class="relative hidden sm:block" x-data="{ o: false }" @click.outside="o = false">
                <button class="btn btn-ghost btn-sm" @click="o = !o" aria-label="{{ __('ui.currency') }}">💱 {{ $cur }}</button>
                <div x-show="o" x-cloak x-transition class="card absolute right-0 mt-2 max-h-80 w-40 overflow-auto p-1">
                    @foreach($currencyService->all() as $c)
                        <form method="POST" action="{{ route('currency', $c->code) }}">@csrf
                            <button class="flex w-full justify-between rounded-lg px-3 py-1.5 text-sm hover:bg-slate-100 dark:hover:bg-white/10 {{ $cur === $c->code ? 'font-bold text-turq-600' : '' }}">
                                <span>{{ $c->code }}</span><span class="text-slate-400">{{ $c->symbol }}</span>
                            </button>
                        </form>
                    @endforeach
                </div>
            </div>
            {{-- Mavzu --}}
            <button class="btn btn-ghost btn-sm" @click="$store.theme.toggle()" aria-label="{{ __('ui.toggle_theme') }}">
                <span x-show="!$store.theme.dark" x-cloak>🌙</span><span x-show="$store.theme.dark" x-cloak>☀️</span>
            </button>

            @auth
                <div class="relative" x-data="{ o: false }" @click.outside="o = false">
                    <button @click="o = !o" class="flex items-center gap-2 rounded-full focus-visible:outline-2" aria-label="{{ __('ui.account') }}">
                        <img src="{{ auth()->user()->avatarUrl() }}" alt="" class="h-9 w-9 rounded-full object-cover ring-2 ring-turq-500/40">
                    </button>
                    <div x-show="o" x-cloak x-transition class="card absolute right-0 mt-2 w-56 p-1">
                        <div class="border-b border-slate-100 px-3 py-2 text-sm dark:border-white/10"><div class="font-semibold">{{ auth()->user()->name }}</div><div class="truncate text-xs text-slate-500">{{ auth()->user()->email }}</div></div>
                        <a class="block rounded-lg px-3 py-2 text-sm hover:bg-slate-100 dark:hover:bg-white/10" href="{{ route('dashboard.index') }}">{{ __('ui.nav_dashboard') }}</a>
                        <a class="block rounded-lg px-3 py-2 text-sm hover:bg-slate-100 dark:hover:bg-white/10" href="{{ route('dashboard.favorites') }}">{{ __('ui.favorites') }}</a>
                        @if(auth()->user()->canAccessAdmin())
                            <a class="block rounded-lg px-3 py-2 text-sm hover:bg-slate-100 dark:hover:bg-white/10" href="{{ route('admin.index') }}">{{ __('ui.nav_admin') }}</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">@csrf
                            <button class="block w-full rounded-lg px-3 py-2 text-left text-sm text-red-600 hover:bg-slate-100 dark:hover:bg-white/10">{{ __('ui.logout') }}</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn btn-ghost btn-sm hidden sm:inline-flex">{{ __('ui.login') }}</a>
                <a href="{{ route('register') }}" class="btn btn-accent btn-sm hidden sm:inline-flex">{{ __('ui.register') }}</a>
            @endauth

            <button class="btn btn-ghost btn-sm 2xl:hidden" @click="menu = !menu" aria-label="{{ __('ui.menu') }}" :aria-expanded="menu">☰</button>
        </div>
    </div>

    {{-- Mobil menyu --}}
    <div x-show="menu" x-cloak x-transition class="border-t border-slate-200 bg-white p-4 dark:border-white/10 dark:bg-night 2xl:hidden">
        <div class="grid grid-cols-2 gap-1">
            @foreach($links as [$route, $key])
                <a href="{{ route($route) }}" class="nav-link">{{ __("ui.$key") }}</a>
            @endforeach
            <a href="{{ route('uzbek-food') }}" class="nav-link">{{ __('ui.nav_uzbek_food') }}</a>
            <a href="{{ route('budget') }}" class="nav-link">{{ __('ui.nav_budget') }}</a>
            <a href="{{ route('currency.index') }}" class="nav-link">{{ __('ui.nav_currency') }}</a>
            <a href="{{ route('guides.index') }}" class="nav-link">{{ __('ui.nav_guides') }}</a>
        </div>
        @guest
            <div class="mt-3 flex gap-2"><a href="{{ route('login') }}" class="btn btn-ghost flex-1">{{ __('ui.login') }}</a><a href="{{ route('register') }}" class="btn btn-accent flex-1">{{ __('ui.register') }}</a></div>
        @endguest
        <div class="mt-3 flex flex-wrap gap-1.5 sm:hidden">
            @foreach($currencyService->all()->take(8) as $c)
                <form method="POST" action="{{ route('currency', $c->code) }}">@csrf<button class="chip {{ $cur === $c->code ? 'chip-active' : '' }}">{{ $c->code }}</button></form>
            @endforeach
        </div>
    </div>
</header>
