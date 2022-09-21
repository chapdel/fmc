<div class="form-field">
    @if($label ?? null)
    <label class="{{ ($required ?? false) ? 'label label-required' : 'label' }}" for="{{ $name }}">
        {{ $label }}
    </label>
    @endif
    <textarea
        @if($disabled ?? false) disabled @endif
        name="{{ $name }}"
        id="{{ $name }}"
        lines="15"
        class="input {{ $inputClass ?? '' }}"
        placeholder="{{ $placeholder ?? '' }}"
    >
        {{ old($name, $value ?? '') }}
    </textarea>
    @error($name)
        <p class="form-error" role="alert">{{ $message }}</p>
    @enderror
</div>
