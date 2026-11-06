@extends('dashboard.layout', ['heading' => __('ui.my_budgets')])
@section('body')
@if($budgets->isEmpty())<x-empty :text="__('ui.no_budgets')" /><a class="btn btn-accent mt-4" href="{{ route('budget') }}">{{ __('ui.nav_budget') }}</a>@else
<div class="space-y-3">@foreach($budgets as $b)@php $t = $b->totalUsd(); @endphp<div class="card flex flex-wrap items-center justify-between gap-3 p-4"><div><div class="font-bold">{{ $b->title }}</div><div class="text-xs text-slate-500">{{ $b->travelers }} {{ __('ui.people') }} · {{ $b->days }} {{ __('ui.days_short') }}</div></div><div class="text-right"><div class="font-extrabold">{{ price($t) }}</div><div class="text-xs text-slate-500">{{ price($t / max(1, $b->travelers)) }} / {{ __('ui.per_person') }}</div></div><x-confirm :action="route('dashboard.budgets.destroy', $b)" /></div>@endforeach</div>
<div class="mt-6">{{ $budgets->links() }}</div>@endif
@endsection
