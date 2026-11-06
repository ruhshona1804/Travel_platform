@if(! $visa)
    <p class="text-sm text-slate-500">{{ __('ui.visa_no_data') }}</p>
@else
    <div class="mb-3 flex flex-wrap items-center gap-2">
        <span class="badge {{ $visa->status === 'visa_free' ? 'badge-ok' : ($visa->status === 'visa_required' ? 'badge-warn' : 'badge-info') }}">{{ __('ui.vs_'.$visa->status) }}</span>
        <span class="badge {{ $visa->verification_status === 'verified' ? 'badge-ok' : 'badge-demo' }}">{{ __('ui.verif_'.$visa->verification_status) }}</span>
    </div>
    <dl class="space-y-2 text-sm">
        @if($visa->max_stay_days)<div class="flex justify-between"><dt class="text-slate-500">{{ __('ui.max_stay') }}</dt><dd class="font-medium">{{ $visa->max_stay_days }} {{ __('ui.days_short') }}</dd></div>@endif
        @if($visa->passport_validity)<div class="flex justify-between gap-3"><dt class="text-slate-500">{{ __('ui.passport_validity') }}</dt><dd class="text-right font-medium">{{ $visa->passport_validity }}</dd></div>@endif
        @if($visa->insurance_required !== null)<div class="flex justify-between"><dt class="text-slate-500">{{ __('ui.insurance') }}</dt><dd class="font-medium">{{ $visa->insurance_required ? __('ui.required') : __('ui.not_required') }}</dd></div>@endif
        <div class="flex justify-between"><dt class="text-slate-500">{{ __('ui.last_verified') }}</dt><dd class="font-medium">{{ $visa->last_verified_at?->format('d.m.Y') ?? __('ui.not_verified') }}</dd></div>
    </dl>
    @if($visa->tr('documents'))<div class="mt-3 text-sm"><div class="font-semibold">{{ __('ui.required_docs') }}</div><div class="prose-lite text-slate-600 dark:text-slate-300">{!! nl2br(e($visa->tr('documents'))) !!}</div></div>@endif
    @if($visa->tr('notes'))<p class="mt-2 text-sm text-slate-600 dark:text-slate-300">{{ $visa->tr('notes') }}</p>@endif
    <div class="mt-3 flex flex-wrap gap-3 text-sm">
        @if($visa->official_url)<a class="font-semibold text-ocean-600 underline dark:text-turq-300" href="{{ $visa->official_url }}" target="_blank" rel="noopener noreferrer">{{ __('ui.official_source') }} ↗</a>@endif
        @if($visa->source_url && $visa->source_url !== $visa->official_url)<a class="text-ocean-600 underline dark:text-turq-300" href="{{ $visa->source_url }}" target="_blank" rel="noopener noreferrer">{{ __('ui.source') }} ↗</a>@endif
    </div>
@endif
<p class="mt-4 rounded-xl bg-amber-50 p-3 text-xs text-amber-900 dark:bg-amber-400/10 dark:text-amber-200">⚠️ {{ __('ui.visa_warning') }}</p>
