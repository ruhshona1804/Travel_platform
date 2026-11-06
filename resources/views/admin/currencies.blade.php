@extends('layouts.admin', ['heading' => __('admin.currencies')])
@section('content')
<div class="mb-4 grid gap-4 sm:grid-cols-3">
    <div class="card p-5"><div class="text-sm text-slate-500">{{ __('admin.provider') }}</div><div class="font-bold">{{ $provider }}</div><div class="mt-1 text-xs text-slate-500">config/travel.php → exchange.provider</div></div>
    <div class="card p-5"><div class="text-sm text-slate-500">{{ __('admin.last_update') }}</div><div class="font-bold">{{ $updatedAt?->format('d.m.Y H:i') ?? '—' }}</div><span class="badge {{ $stale ? 'badge-demo' : 'badge-ok' }} mt-1">{{ $stale ? __('admin.stale') : __('admin.fresh') }}</span></div>
    <form method="POST" action="{{ route('admin.currencies.refresh') }}" class="card flex items-center p-5">@csrf<button class="btn btn-accent w-full">🔄 {{ __('admin.refresh_now') }}</button></form>
</div>
<div class="card overflow-x-auto"><table class="table-x min-w-[760px]"><thead><tr><th>{{ __('admin.f_code') }}</th><th>{{ __('ui.name') }}</th><th>{{ __('admin.rate_per_usd') }}</th><th>{{ __('admin.source') }}</th><th>{{ __('admin.manual') }}</th><th>{{ __('admin.active') }}</th><th></th></tr></thead>
<tbody>@foreach($currencies as $c)<tr><form method="POST" action="{{ route('admin.currencies.update', $c) }}">@csrf @method('PUT')
    <td class="font-bold">{{ $c->code }}</td><td>{{ $c->name }}</td>
    <td><input name="rate_per_usd" type="number" step="any" min="0.00000001" value="{{ rtrim(rtrim(number_format($c->rate_per_usd, 8, '.', ''), '0'), '.') }}" class="input !w-40" @readonly($c->code === 'USD')></td>
    <td><span class="badge {{ $c->source === 'api' ? 'badge-ok' : 'badge-demo' }}">{{ $c->source }}</span></td>
    <td><input type="checkbox" name="is_manual" value="1" @checked($c->is_manual) @disabled($c->code === 'USD')></td>
    <td><input type="checkbox" name="is_active" value="1" @checked($c->is_active)></td>
    <td><button class="btn btn-primary btn-sm">{{ __('ui.save') }}</button></td></form></tr>@endforeach</tbody></table></div>
<p class="mt-3 text-xs text-slate-500">ℹ️ {{ __('admin.currency_note') }}</p>
@endsection
