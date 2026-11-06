@extends('layouts.admin', ['heading' => __("admin.$resource")])
@php
    $locales = config('travel.locales'); $isNew = ! $row->exists;
    $optLabel = function (string $o): string {
        foreach (['cat_', 'vs_', 'continent_', 'tr_', 'verif_'] as $prefix) {
            $t = __('ui.'.$prefix.$o);
            if ($t !== 'ui.'.$prefix.$o) return $t;
        }
        return $o;
    };
@endphp
@section('content')
<form method="POST" enctype="multipart/form-data" action="{{ $isNew ? route('admin.resource.store', $resource) : route('admin.resource.update', [$resource, $row->getKey()]) }}" class="card grid gap-5 p-6 md:grid-cols-2">
    @csrf @unless($isNew)@method('PUT')@endunless
    @foreach($cfg['fields'] as $f)
        @php $n = $f['name']; $label = __("admin.f_$n"); $wide = in_array($f['type'], ['tr', 'trtext', 'images', 'multi', 'kv']); $val = old($n, $row->$n ?? ($f['default'] ?? null)); @endphp
        <div class="{{ $wide ? 'md:col-span-2' : '' }}">
            @switch($f['type'])
                @case('tr') @case('trtext')
                    <div x-data="{ l: 'uz' }"><div class="mb-1.5 flex flex-wrap items-center gap-2"><span class="label !mb-0">{{ $label }}@if(!empty($f['required'])) <span class="text-coral-500">*</span>@endif</span>
                        @foreach($locales as $code => $l)@php $has = ! empty(($row->$n ?? [])[$code] ?? null); @endphp<button type="button" @click="l = '{{ $code }}'" :class="l === '{{ $code }}' ? 'chip-active' : ''" class="chip !px-2 !py-0.5 text-xs">{{ strtoupper($code) }}@if($has) ·@endif</button>@endforeach</div>
                        @foreach($locales as $code => $l)<div x-show="l === '{{ $code }}'" @if($code !== 'uz') x-cloak @endif>
                            @if($f['type'] === 'tr')<input name="{{ $n }}[{{ $code }}]" value="{{ old("$n.$code", ($row->$n ?? [])[$code] ?? '') }}" class="input" placeholder="{{ $l['name'] }}">
                            @else<textarea name="{{ $n }}[{{ $code }}]" rows="5" class="input" placeholder="{{ $l['name'] }}">{{ old("$n.$code", ($row->$n ?? [])[$code] ?? '') }}</textarea>@endif</div>@endforeach
                        @error("$n.uz")<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        <p class="mt-1 text-xs text-slate-500">{{ __('admin.translation_hint') }}</p></div>
                    @break
                @case('textarea')<label class="label" for="{{ $n }}">{{ $label }}</label><textarea id="{{ $n }}" name="{{ $n }}" rows="4" class="input">{{ $val }}</textarea>@break
                @case('select')<label class="label" for="{{ $n }}">{{ $label }}</label><select id="{{ $n }}" name="{{ $n }}" class="input">@foreach($f['options'] as $o)<option value="{{ $o }}" @selected($val === $o)>{{ $optLabel($o) }}</option>@endforeach</select>@break
                @case('relation')<label class="label" for="{{ $n }}">{{ $label }}</label><select id="{{ $n }}" name="{{ $n }}" class="input"><option value="">—</option>@foreach($f['model']::orderBy('id')->get() as $o)<option value="{{ $o->id }}" @selected((string) $val === (string) $o->id)>{{ is_array($o->name) ? $o->tr('name') : $o->name }}</option>@endforeach</select>@break
                @case('bool')<label class="flex items-center gap-2 pt-7 text-sm font-medium"><input type="hidden" name="{{ $n }}" value="0"><input type="checkbox" name="{{ $n }}" value="1" @checked((bool) $val)> {{ $label }}</label>@break
                @case('image')<label class="label" for="{{ $n }}">{{ $label }}</label>@if($row->$n ?? null)<div class="mb-2 flex items-center gap-3"><img src="{{ img($row->$n) }}" alt="" class="h-16 w-24 rounded-lg object-cover"><label class="flex items-center gap-1 text-xs"><input type="checkbox" name="remove_{{ $n }}" value="1"> {{ __('admin.remove') }}</label></div>@endif<input id="{{ $n }}" type="file" name="{{ $n }}" accept="image/*" class="input">@break
                @case('images')<label class="label" for="{{ $n }}">{{ $label }}</label><div class="mb-2 flex flex-wrap gap-3">@foreach(($row->$n ?? []) as $g)<label class="text-xs"><img src="{{ img($g) }}" alt="" class="h-16 w-24 rounded-lg object-cover"><input type="checkbox" name="remove_{{ $n }}[]" value="{{ $g }}"> {{ __('admin.remove') }}</label>@endforeach</div><input id="{{ $n }}" type="file" name="{{ $n }}[]" accept="image/*" multiple class="input">@break
                @case('multi')@php $cur = is_array($val) ? $val : array_filter(explode(',', (string) $val)); @endphp<span class="label">{{ $label }}</span><div class="flex flex-wrap gap-2">@foreach($f['options'] as $o)<label class="chip cursor-pointer has-[:checked]:chip-active"><input type="checkbox" class="sr-only" name="{{ $n }}[]" value="{{ $o }}" @checked(in_array($o, $cur))>{{ __("ui.season_$o") !== "ui.season_$o" ? __("ui.season_$o") : __("ui.style_$o") }}</label>@endforeach</div>@break
                @case('csv')<label class="label" for="{{ $n }}">{{ $label }} <span class="text-xs text-slate-400">({{ __('admin.csv_hint') }})</span></label><input id="{{ $n }}" name="{{ $n }}" class="input" value="{{ old($n, is_array($row->$n ?? null) ? implode(', ', $row->$n) : '') }}">@break
                @case('kv')<label class="label" for="{{ $n }}">{{ $label }} <span class="text-xs text-slate-400">({{ __('admin.kv_hint') }})</span></label><textarea id="{{ $n }}" name="{{ $n }}" rows="3" class="input">{{ old($n, collect($row->$n ?? [])->map(fn ($v, $k) => "$k=$v")->implode("\n")) }}</textarea>@break
                @default
                    <label class="label" for="{{ $n }}">{{ $label }}</label>
                    <input id="{{ $n }}" name="{{ $n }}" type="{{ in_array($f['type'], ['number', 'url', 'date', 'time']) ? $f['type'] : 'text' }}" @if(isset($f['step']))step="{{ $f['step'] }}"@endif value="{{ $f['type'] === 'date' && $val instanceof \Carbon\Carbon ? $val->format('Y-m-d') : ($f['type'] === 'time' && $val ? substr($val, 0, 5) : $val) }}" class="input">
            @endswitch
            @if(! in_array($f['type'], ['tr', 'trtext']))@error($n)<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror @endif
        </div>
    @endforeach
    <div class="flex gap-2 md:col-span-2"><button class="btn btn-primary">{{ __('ui.save') }}</button><a href="{{ route('admin.resource.index', $resource) }}" class="btn btn-ghost">{{ __('ui.cancel') }}</a></div>
</form>
@endsection
