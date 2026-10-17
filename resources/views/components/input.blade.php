@props(['name', 'label', 'type' => 'text', 'value' => null, 'required' => false, 'rows' => 3, 'help' => null, 'id' => null])
<div class="mb-3">
    <label for="{{ $id ?? $name }}" class="form-label">{{ $label }}@if($required)<span class="text-danger"> *</span>@endif</label>
    @if($type === 'textarea')
        <textarea id="{{ $id ?? $name }}" name="{{ $name }}" rows="{{ $rows }}" {{ $attributes->class(['form-control', 'is-invalid' => $errors->has($name)]) }}>{{ old($name, $value) }}</textarea>
    @else
        <input id="{{ $id ?? $name }}" type="{{ $type }}" name="{{ $name }}" value="{{ $type === 'password' ? '' : old($name, $value) }}" {{ $attributes->class(['form-control', 'is-invalid' => $errors->has($name)]) }}>
    @endif
    @if($help)<div class="form-text">{{ $help }}</div>@endif
    @error($name)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
