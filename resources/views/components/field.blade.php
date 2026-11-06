@props(['name', 'label', 'type' => 'text', 'value' => null, 'required' => false, 'autocomplete' => null])
<div>
    <label for="{{ $name }}" class="label">{{ $label }}@if($required) <span class="text-coral-500">*</span>@endif</label>
    <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ $type === 'password' ? '' : old($name, $value) }}" @required($required) @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif {{ $attributes->merge(['class' => 'input']) }} @error($name) aria-invalid="true" @enderror>
    @error($name)<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>
