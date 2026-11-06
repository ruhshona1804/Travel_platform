@php $items = [['dashboard.index','ui.nav_dashboard','🏠'],['dashboard.favorites','ui.favorites','❤️'],['dashboard.plans','ui.my_plans','🗺️'],['dashboard.budgets','ui.my_budgets','🧮'],['dashboard.notifications','ui.notifications','🔔'],['dashboard.profile','ui.profile','⚙️']]; @endphp
<nav class="card flex gap-1 overflow-x-auto p-2 lg:flex-col" aria-label="{{ __('ui.nav_dashboard') }}">
    @foreach($items as [$r, $l, $e])<a href="{{ route($r) }}" class="nav-link whitespace-nowrap {{ request()->routeIs($r) ? 'nav-link-active' : '' }}">{{ $e }} {{ __($l) }}</a>@endforeach
</nav>
