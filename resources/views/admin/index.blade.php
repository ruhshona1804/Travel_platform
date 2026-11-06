@extends('layouts.admin', ['heading' => __('admin.dashboard')])
@section('content')
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    @foreach([['users','👥'],['countries','🌍'],['cities','🏙️'],['attractions','📍'],['hotels','🏨'],['restaurants','🍽️'],['plans','🗺️']] as [$k,$e])
        <div class="card p-5"><div class="text-2xl">{{ $e }}</div><div class="mt-2 text-3xl font-extrabold">{{ $stats[$k] }}</div><div class="text-sm text-slate-500">{{ __("admin.stat_$k") }}</div></div>
    @endforeach
</div>
<div class="mt-6 grid gap-6 lg:grid-cols-2">
    <section class="card p-5"><h2 class="mb-3 font-bold">{{ __('admin.recent_users') }}</h2><table class="table-x"><tbody>@foreach($recentUsers as $u)<tr><td>{{ $u->name }}<div class="text-xs text-slate-500">{{ $u->email }}</div></td><td><span class="badge badge-info">{{ __('admin.role_'.$u->role) }}</span></td><td class="text-right text-xs text-slate-500">{{ $u->created_at->diffForHumans() }}</td></tr>@endforeach</tbody></table></section>
    <section class="card p-5"><h2 class="mb-3 font-bold">{{ __('admin.recent_activity') }}</h2>@forelse($activity as $a)<div class="flex justify-between border-b border-slate-100 py-2 text-sm last:border-0 dark:border-white/5"><span><b>{{ $a->user?->name ?? '—' }}</b> · {{ $a->action }} {{ $a->subject }}</span><span class="text-xs text-slate-500">{{ $a->created_at->diffForHumans() }}</span></div>@empty<p class="text-sm text-slate-500">{{ __('ui.empty') }}</p>@endforelse</section>
</div>
@endsection
