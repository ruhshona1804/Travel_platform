@props(['usd', 'verified' => false, 'demo' => false, 'suffix' => null])
@if($usd === null)
    <span class="text-slate-400">{{ __('ui.price_unknown') }}</span>
@else
    <span class="font-semibold">{{ price((float) $usd) }}</span>@if($suffix)<span class="text-xs text-slate-500"> {{ $suffix }}</span>@endif
    @unless($verified)<span class="badge badge-demo ml-1" title="{{ __('ui.estimate_hint') }}">{{ __('ui.estimate') }}</span>@endunless
@endif
