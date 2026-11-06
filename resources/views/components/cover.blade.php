@props(['model', 'class' => 'h-48', 'emoji' => '🌍'])
@php
    $src = method_exists($model, 'imageUrl') ? $model->imageUrl() : null;
    $seed = crc32($model->slug ?? (string) $model->id);
    $h1 = $seed % 360; $h2 = ($h1 + 40) % 360;
@endphp
<div {{ $attributes->merge(['class' => "relative overflow-hidden $class"]) }}>
    @if($src)
        <img src="{{ $src }}" alt="{{ $model->tr('name') }}" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
    @else
        <div class="grid h-full w-full place-items-center text-5xl" style="background: linear-gradient(135deg, hsl({{ $h1 }} 65% 38%), hsl({{ $h2 }} 70% 55%));" role="img" aria-label="{{ $model->tr('name') }}">
            <span class="drop-shadow-lg">{{ $model instanceof \App\Models\Country ? $model->flag() : $emoji }}</span>
        </div>
    @endif
</div>
