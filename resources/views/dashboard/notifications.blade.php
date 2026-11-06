@extends('dashboard.layout', ['heading' => __('ui.notifications')])
@section('body')
@if($notifications->isEmpty())<x-empty :text="__('ui.no_notifications')" />@else
<form method="POST" action="{{ route('dashboard.notifications.read') }}" class="mb-4">@csrf<button class="btn btn-ghost btn-sm">{{ __('ui.mark_read') }}</button></form>
<div class="space-y-3">@foreach($notifications as $n)<a href="{{ $n->data['url'] ?? '#' }}" class="card block p-4 {{ $n->read_at ? 'opacity-70' : 'border-turq-500/50' }}"><div class="font-semibold">{{ $n->data['title'] ?? '' }}</div><div class="text-sm text-slate-500">{{ $n->data['body'] ?? '' }}</div><div class="mt-1 text-xs text-slate-400">{{ $n->created_at->diffForHumans() }}</div></a>@endforeach</div>
<div class="mt-6">{{ $notifications->links() }}</div>@endif
@endsection
