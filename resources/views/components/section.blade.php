@props(['title', 'sub' => null, 'href' => null, 'more' => null])
<section {{ $attributes->merge(['class' => 'container-x mt-16']) }}>
    <div class="mb-6 flex items-end justify-between gap-4">
        <div><h2 class="section-title">{{ $title }}</h2>@if($sub)<p class="mt-1 text-slate-500 dark:text-slate-400">{{ $sub }}</p>@endif</div>
        @if($href)<a href="{{ $href }}" class="shrink-0 text-sm font-semibold text-ocean-600 hover:underline dark:text-turq-300">{{ $more ?? __('ui.see_all') }} →</a>@endif
    </div>
    {{ $slot }}
</section>
