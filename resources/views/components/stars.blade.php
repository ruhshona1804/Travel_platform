@props(['value'])
@if($value)<span class="inline-flex items-center gap-1 text-sm font-semibold text-amber-500" title="{{ __('ui.rating') }}">★ {{ number_format((float) $value, 1) }}</span>@endif
