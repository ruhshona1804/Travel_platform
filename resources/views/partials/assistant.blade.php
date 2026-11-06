<div x-data="assistant('{{ route('assistant.ask') }}')" class="fixed bottom-4 right-4 z-40">
    <div x-show="open" x-cloak x-transition class="card mb-3 flex h-[28rem] w-[calc(100vw-2rem)] max-w-sm flex-col overflow-hidden shadow-2xl">
        <div class="flex items-center justify-between bg-gradient-to-r from-ocean-700 to-turq-600 px-4 py-3 text-white">
            <div class="font-semibold">🤖 {{ __('ui.ai_title') }}</div>
            <button @click="open = false" aria-label="{{ __('ui.close') }}">✕</button>
        </div>
        <div x-ref="box" class="flex-1 space-y-3 overflow-y-auto p-4 text-sm">
            <div class="rounded-2xl bg-slate-100 p-3 dark:bg-white/10">{{ __('ui.ai_greeting') }}</div>
            <template x-for="(m, i) in log" :key="i">
                <div :class="m.me ? 'ml-8 bg-ocean-600 text-white' : 'mr-8 bg-slate-100 dark:bg-white/10'" class="whitespace-pre-line rounded-2xl p-3" x-text="m.t"></div>
            </template>
            <div x-show="busy" class="skeleton h-8 w-24"></div>
        </div>
        <form @submit.prevent="send" class="flex gap-2 border-t border-slate-200 p-3 dark:border-white/10">
            <input x-model="msg" class="input" maxlength="1500" placeholder="{{ __('ui.ai_placeholder') }}" aria-label="{{ __('ui.ai_placeholder') }}">
            <button class="btn btn-primary" :disabled="busy">➤</button>
        </form>
    </div>
    <button @click="open = !open" class="btn btn-accent ml-auto !h-14 !w-14 !rounded-full text-xl" aria-label="{{ __('ui.ai_title') }}">💬</button>
</div>
