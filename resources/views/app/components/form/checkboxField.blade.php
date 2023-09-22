@php($hidden = $hidden ?? false)

@if(! $hidden)
    <label class="checkbox-label" for="{{ $name }}">
@endif
    <input
    type="{{ $hidden ? 'hidden' : 'checkbox' }}"
    name="{{ $name }}"
    id="{{ $name }}"
    value="{{ $value ?? 1 }}"
    @if(old($name, $checked ?? false)) checked @endif
    @if($disabled ?? false) disabled @endif
    {{ $attributes->class('checkbox') }}
    >
@if(! $hidden)
        <span>{{ $label }}</span>
    </label>
@endif

@error($name)
    <p class="form-error" role="alert">{{ $message }}</p>
@enderror
