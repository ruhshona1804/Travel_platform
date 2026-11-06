@extends('dashboard.layout', ['heading' => __('ui.welcome_user', ['name' => auth()->user()->name])])
@section('body')
<div class="grid gap-4 sm:grid-cols-4">
    @foreach([[$favCount, 'favorites', 'dashboard.favorites'], [$planCount, 'my_plans', 'dashboard.plans'], [$budgetCount, 'my_budgets', 'dashboard.budgets'], [$unread, 'notifications', 'dashboard.notifications']] as [$n, $l, $r])
        <a href="{{ route($r) }}" class="card card-hover p-5"><div class="text-3xl font-extrabold text-ocean-700 dark:text-turq-300">{{ $n }}</div><div class="text-sm text-slate-500">{{ __("ui.$l") }}</div></a>
    @endforeach
</div>
<h2 class="mb-3 mt-8 text-lg font-bold">{{ __('ui.recent_plans') }}</h2>
@forelse($plans as $p)<a href="{{ route('planner.show', $p) }}" class="card card-hover mb-3 flex items-center justify-between p-4"><span class="font-semibold">{{ $p->title }}</span><span class="text-sm text-slate-500">{{ price((float) $p->total_usd) }}</span></a>
@empty<x-empty :text="__('ui.no_plans')" /><a class="btn btn-accent mt-4" href="{{ route('planner') }}">{{ __('ui.planner_cta') }}</a>@endforelse
@endsection
