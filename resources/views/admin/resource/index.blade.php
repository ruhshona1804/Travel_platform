@extends('layouts.admin', ['heading' => __("admin.$resource")])
@section('content')
<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
    <form class="flex gap-2"><input name="q" value="{{ request('q') }}" class="input w-64" placeholder="{{ __('ui.search') }}" aria-label="{{ __('ui.search') }}"><button class="btn btn-ghost">{{ __('ui.search') }}</button></form>
    <a class="btn btn-primary" href="{{ route('admin.resource.create', $resource) }}">+ {{ __('admin.add') }}</a>
</div>
<div class="card overflow-x-auto"><table class="table-x min-w-[640px]">
    <thead><tr>@foreach($cfg['columns'] as $c)@php $n = explode(':', $c)[0]; @endphp<th>{{ __("admin.f_$n") }}</th>@endforeach<th></th></tr></thead>
    <tbody>@forelse($rows as $row)<tr>
        @foreach($cfg['columns'] as $c)@php [$n, $t] = array_pad(explode(':', $c), 2, null); @endphp
            <td>@if($t === 'tr'){{ $row->tr($n) }}@elseif($t === 'relation')@php $rel = \Illuminate\Support\Str::beforeLast($n, '_id'); $rel = \Illuminate\Support\Str::camel($rel); $r = $row->$rel; @endphp{{ $r ? (is_array($r->getAttribute('name')) ? $r->tr('name') : $r->name) : '—' }}@else @php $v = $row->$n; @endphp @if(is_bool($v))<span class="badge {{ $v ? 'badge-ok' : 'badge-demo' }}">{{ $v ? '✓' : '—' }}</span>@else{{ $v instanceof \Carbon\Carbon ? $v->format('d.m.Y') : $v }}@endif @endif</td>
        @endforeach
        <td class="whitespace-nowrap text-right"><a class="btn btn-ghost btn-sm" href="{{ route('admin.resource.edit', [$resource, $row->getKey()]) }}">{{ __('ui.edit') }}</a>
            @can('manage-system')<x-confirm :action="route('admin.resource.destroy', [$resource, $row->getKey()])" />@endcan</td></tr>
    @empty<tr><td colspan="{{ count($cfg['columns']) + 1 }}" class="py-8 text-center text-slate-500">{{ __('ui.empty') }}</td></tr>@endforelse</tbody></table></div>
<div class="mt-4">{{ $rows->links() }}</div>
@endsection
