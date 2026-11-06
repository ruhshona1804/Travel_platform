@props(['country'])
<a href="{{ route('countries.show', $country) }}" class="card card-hover group block overflow-hidden">
    <x-cover :model="$country" class="h-40" />
    <div class="p-4">
        <div class="flex items-center justify-between">
            <h3 class="font-bold">{{ $country->flag() }} {{ $country->tr('name') }}</h3>
            @if($country->visa)<span class="badge {{ $country->visa->status === 'visa_free' ? 'badge-ok' : 'badge-info' }}">{{ __('ui.vs_'.$country->visa->status) }}</span>@endif
        </div>
        <p class="mt-1 text-xs text-slate-500">{{ $country->tr('capital') }}</p>
        @if($country->daily_budget_budget)<p class="mt-2 text-sm">{{ __('ui.from_per_day') }} <x-price :usd="$country->daily_budget_budget" :verified="false" /></p>@endif
    </div>
</a>
