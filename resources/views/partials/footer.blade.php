<footer class="mt-16 bg-ocean-900 text-slate-300">
    <div class="container-x grid gap-10 py-12 md:grid-cols-4">
        <div class="md:col-span-1">
            <div class="flex items-center gap-2 text-xl font-extrabold text-white"><span class="grid h-9 w-9 place-items-center rounded-xl bg-gradient-to-br from-ocean-500 to-turq-500">🧭</span>{{ app_name() }}</div>
            <p class="mt-3 text-sm leading-relaxed text-slate-400">{{ setting('footer_text') ?: __('ui.footer_about') }}</p>
        </div>
        <div>
            <h3 class="mb-3 font-semibold text-white">{{ __('ui.footer_explore') }}</h3>
            <ul class="space-y-2 text-sm">
                <li><a class="hover:text-turq-300" href="{{ route('countries.index') }}">{{ __('ui.nav_countries') }}</a></li>
                <li><a class="hover:text-turq-300" href="{{ route('attractions.index') }}">{{ __('ui.nav_attractions') }}</a></li>
                <li><a class="hover:text-turq-300" href="{{ route('hotels.index') }}">{{ __('ui.nav_hotels') }}</a></li>
                <li><a class="hover:text-turq-300" href="{{ route('restaurants.index') }}">{{ __('ui.nav_restaurants') }}</a></li>
                <li><a class="hover:text-turq-300" href="{{ route('uzbek-food') }}">{{ __('ui.nav_uzbek_food') }}</a></li>
            </ul>
        </div>
        <div>
            <h3 class="mb-3 font-semibold text-white">{{ __('ui.footer_tools') }}</h3>
            <ul class="space-y-2 text-sm">
                <li><a class="hover:text-turq-300" href="{{ route('planner') }}">{{ __('ui.nav_planner') }}</a></li>
                <li><a class="hover:text-turq-300" href="{{ route('budget') }}">{{ __('ui.nav_budget') }}</a></li>
                <li><a class="hover:text-turq-300" href="{{ route('currency.index') }}">{{ __('ui.nav_currency') }}</a></li>
                <li><a class="hover:text-turq-300" href="{{ route('flights.compare') }}">{{ __('ui.compare_flights') }}</a></li>
                <li><a class="hover:text-turq-300" href="{{ route('map') }}">{{ __('ui.nav_map') }}</a></li>
                <li><a class="hover:text-turq-300" href="{{ route('guides.index') }}">{{ __('ui.nav_guides') }}</a></li>
            </ul>
        </div>
        <div>
            <h3 class="mb-3 font-semibold text-white">{{ __('ui.footer_info') }}</h3>
            <p class="text-sm leading-relaxed text-slate-400">{{ __('ui.footer_disclaimer') }}</p>
            @if($m = setting('contact_email'))<p class="mt-3 text-sm"><a class="hover:text-turq-300" href="mailto:{{ $m }}">{{ $m }}</a></p>@endif
        </div>
    </div>
    <div class="border-t border-white/10 py-5 text-center text-xs text-slate-500">
        © {{ date('Y') }} {{ app_name() }}. {{ __('ui.footer_rights') }} · {{ __('ui.map_credit') }} <a class="underline" href="https://www.openstreetmap.org/copyright" rel="noopener">OpenStreetMap</a>
    </div>
</footer>
