@props([
    'label' => null,
    'name' => null,
    'required' => false,
    'dataConditional' => null,
    'placeholder' => null,
    'options' => [],
    'value' => null,
])
<div class="form-field">
    @if($label)
        <label class="{{ ($required ?? false) ? 'label label-required' : 'label' }}" for="{{ $name }}">
            {{ $label }}
        </label>
    @endif
    <div class="select">
        <select
            name="{{ $name }}"
            id="{{ $name }}"
            {{ $required ? 'required' : '' }}
            @if($dataConditional) data-conditional="{{ $dataConditional }}" @endif
            {{ $attributes->except(['options']) }}
        >
            @if($placeholder)
                <option value="" disabled hidden @unless(old($name, $value)) selected @endunless>{{ $placeholder }}</option>
            @endif
            @foreach($options as $optionValue => $label)
                <option value="{{ $optionValue }}" @if(old($name, $value) == $optionValue) selected @endif>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        <div class="select-arrow">
            <i class="fas fa-angle-down"></i>
        </div>
    </div>
    @error($name)
        <p class="form-error" role="alert">{{ $message }}</p>
    @enderror
</div>
