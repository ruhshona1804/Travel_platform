@extends('layouts.admin', ['heading' => $user->name])
@section('content')
<div class="grid gap-6 lg:grid-cols-3">
    <section class="card p-6"><div class="flex items-center gap-3"><img src="{{ $user->avatarUrl() }}" class="h-14 w-14 rounded-full" alt=""><div><div class="font-bold">{{ $user->name }}</div><div class="text-sm text-slate-500">{{ $user->email }}</div></div></div>
        <dl class="mt-4 space-y-1 text-sm"><div class="flex justify-between"><dt class="text-slate-500">{{ __('admin.registered') }}</dt><dd>{{ $user->created_at->format('d.m.Y H:i') }}</dd></div><div class="flex justify-between"><dt class="text-slate-500">{{ __('ui.verify_email') }}</dt><dd>{{ $user->email_verified_at ? '✓' : '—' }}</dd></div><div class="flex justify-between"><dt class="text-slate-500">{{ __('ui.my_plans') }}</dt><dd>{{ $plans }}</dd></div><div class="flex justify-between"><dt class="text-slate-500">{{ __('ui.favorites') }}</dt><dd>{{ $favs }}</dd></div></dl></section>
    <section class="card p-6"><h2 class="mb-3 font-bold">{{ __('admin.edit_user') }}</h2>
        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-3">@csrf @method('PUT')
            <div><label class="label" for="role">{{ __('admin.f_role') }}</label><select id="role" name="role" class="input">@foreach(\App\Models\User::ROLES as $r)@if($r !== 'super_admin' || auth()->user()->isSuperAdmin())<option value="{{ $r }}" @selected($user->role === $r)>{{ __("admin.role_$r") }}</option>@endif @endforeach</select></div>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_suspended" value="1" @checked($user->is_suspended)> {{ __('admin.suspend_account') }}</label>
            <button class="btn btn-primary">{{ __('ui.save') }}</button></form>
        @if($user->id !== auth()->id())<div class="mt-4 border-t border-slate-100 pt-4 dark:border-white/10"><x-confirm :action="route('admin.users.destroy', $user)" :label="__('admin.delete_account')" /></div>@endif</section>
    <section class="card p-6"><h2 class="mb-3 font-bold">{{ __('admin.recent_activity') }}</h2>@forelse($logs as $l)<div class="flex justify-between border-b border-slate-100 py-1.5 text-sm last:border-0 dark:border-white/5"><span>{{ $l->action }} {{ $l->subject }}</span><span class="text-xs text-slate-500">{{ $l->created_at->diffForHumans() }}</span></div>@empty<p class="text-sm text-slate-500">{{ __('ui.empty') }}</p>@endforelse</section>
</div>
@endsection
