@if(session('status') || session('error') || $errors->any())
    <div class="fixed right-4 top-20 z-50 w-[calc(100%-2rem)] max-w-sm space-y-2" aria-live="polite">
        @if(session('status'))
            <div x-data="toast" x-show="show" x-transition class="card flex items-start gap-3 border-emerald-300 p-4 text-sm shadow-lg">
                <span>✅</span><div class="flex-1">{{ session('status') }}</div><button @click="show = false" aria-label="{{ __('ui.close') }}">✕</button>
            </div>
        @endif
        @if(session('error'))
            <div x-data="toast" x-show="show" x-transition class="card flex items-start gap-3 border-red-300 p-4 text-sm shadow-lg">
                <span>⚠️</span><div class="flex-1">{{ session('error') }}</div><button @click="show = false" aria-label="{{ __('ui.close') }}">✕</button>
            </div>
        @endif
        @if($errors->any() && ! request()->routeIs('login', 'register', 'password.*'))
            <div x-data="toast" x-show="show" x-transition class="card border-red-300 p-4 text-sm shadow-lg">
                <ul class="list-disc pl-4">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif
    </div>
@endif
