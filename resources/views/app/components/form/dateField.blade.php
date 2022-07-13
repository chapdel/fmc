@props([
    'minDate' => 'today',
    'maxDate' => null,
    'position' => 'above',
    'label' => null,
    'required' => false,
    'name' => null,
    'class' => '',
    'inputClass' => '',
    'value' => '',
    'placeholder' => '',
    'disabled' => false,
])
<div
    x-data="{
        value: '{{ $value }}',
        init() {
            let picker = flatpickr(this.$refs.picker, {
                dateFormat: 'Y-m-d',
                defaultDate: this.value,
                @if($minDate) minDate: '{{ $minDate }}', @endif
                @if($maxDate) maxDate: '{{ $maxDate }}', @endif
                position: '{{ $position }}',
            })

            this.$watch('value', () => picker.setDate(this.value))
        },
    }"
    class="form-field {{ $class }}"
>
    @if($label)
    <label class="{{ $required ? 'label label-required' : 'label' }}" for="{{ $name }}">
        {{ $label }}
    </label>
    @endif
    <input
        x-ref="picker"
        type="text"
        name="{{ $name }}"
        id="{{ $name }}"
        class="input max-w-xs {{ $inputClass }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {!! $attributes ?? '' !!}
        @if($disabled) disabled @endif
    >
    @error($name)
        <p class="form-error" role="alert">{{ $message }}</p>
    @enderror
</div>
