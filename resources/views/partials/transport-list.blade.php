<div class="grid gap-3 sm:grid-cols-2">
@foreach($transport as $t)
    <div class="rounded-xl border border-slate-200 p-4 dark:border-white/10">
        <div class="flex items-start justify-between gap-2"><div class="font-semibold">{{ __('ui.tr_'.$t->type) }} · {{ $t->name }}</div>@if($t->is_demo)<span class="badge badge-demo">{{ __('ui.demo') }}</span>@endif</div>
        @if(! empty($showCity))<div class="text-xs text-slate-500">{{ $t->city->tr('name') }}</div>@endif
        @if($t->tr('description'))<p class="mt-1 text-sm text-slate-600 dark:text-slate-300">{{ $t->tr('description') }}</p>@endif
        <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm">
            @if($t->start_fare_usd !== null)<span>{{ __('ui.start_fare') }}: <x-price :usd="$t->start_fare_usd" :verified="$t->price_verified" /></span>@endif
            @if($t->per_km_usd !== null)<span>{{ __('ui.per_km') }}: <x-price :usd="$t->per_km_usd" :verified="$t->price_verified" /></span>@endif
            @if($t->website)<a class="text-ocean-600 underline dark:text-turq-300" href="{{ $t->website }}" target="_blank" rel="noopener noreferrer">{{ __('ui.website') }} ↗</a>@endif
        </div>
    </div>
@endforeach
</div>
