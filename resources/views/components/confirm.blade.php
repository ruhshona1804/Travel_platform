@props(['action', 'method' => 'DELETE', 'label' => null, 'message' => null, 'class' => 'btn btn-danger btn-sm', 'password' => false])
<div x-data="{ o: false }" class="inline">
    <button type="button" @click="o = true" class="{{ $class }}">{{ $label ?? __('ui.delete') }}</button>
    <template x-teleport="body">
        <div x-show="o" x-cloak class="fixed inset-0 z-[60] grid place-items-center bg-black/50 p-4" @keydown.escape.window="o = false" role="dialog" aria-modal="true">
            <form method="POST" action="{{ $action }}" @click.outside="o = false" class="card w-full max-w-sm p-6 shadow-2xl">
                @csrf @method($method)
                <h3 class="text-lg font-bold">{{ __('ui.confirm_title') }}</h3>
                <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">{{ $message ?? __('ui.confirm_delete') }}</p>
                @if($password)<input type="password" name="password" required class="input mt-3" placeholder="{{ __('ui.password') }}">@endif
                <div class="mt-5 flex justify-end gap-2">
                    <button type="button" @click="o = false" class="btn btn-ghost">{{ __('ui.cancel') }}</button>
                    <button class="btn btn-danger">{{ __('ui.confirm') }}</button>
                </div>
            </form>
        </div>
    </template>
</div>
