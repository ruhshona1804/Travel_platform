@extends('dashboard.layout', ['heading' => __('ui.my_plans')])
@section('body')
@if($plans->isEmpty())<x-empty :text="__('ui.no_plans')" />@else
<div class="space-y-3">@foreach($plans as $p)<div class="card flex flex-wrap items-center justify-between gap-3 p-4"><div><a href="{{ route('planner.show', $p) }}" class="font-bold hover:text-ocean-600">{{ $p->title }}</a><div class="text-xs text-slate-500">{{ $p->start_date?->format('d.m.Y') }} – {{ $p->end_date?->format('d.m.Y') }} · {{ price((float) $p->total_usd) }}</div></div>
    <div class="flex gap-2"><a class="btn btn-ghost btn-sm" href="{{ route('planner.show', $p) }}">{{ __('ui.open') }}</a><x-confirm :action="route('dashboard.plans.destroy', $p)" /></div></div>@endforeach</div>
<div class="mt-6">{{ $plans->links() }}</div>@endif
@endsection
