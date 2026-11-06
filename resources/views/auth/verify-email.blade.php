@extends('layouts.app')
@section('title', __('ui.verify_email'))
@section('content')
<div class="container-x grid min-h-[60vh] place-items-center py-10"><div class="card w-full max-w-md p-8 text-center shadow-xl">
    <div class="text-5xl">✉️</div><h1 class="mt-3 text-2xl font-extrabold">{{ __('ui.verify_email') }}</h1><p class="mt-2 text-sm text-slate-500">{{ __('ui.verify_sub') }}</p>
    @if(session('status'))<div class="mt-4 rounded-xl bg-emerald-50 p-3 text-sm text-emerald-800">{{ session('status') }}</div>@endif
    <form method="POST" action="{{ route('verification.send') }}" class="mt-5">@csrf<button class="btn btn-primary">{{ __('ui.resend_verification') }}</button></form>
    <a href="{{ route('dashboard.index') }}" class="mt-4 inline-block text-sm text-ocean-600">{{ __('ui.continue') }} →</a>
</div></div>
@endsection
