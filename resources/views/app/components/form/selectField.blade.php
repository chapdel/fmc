@props([
    'label' => null,
    'name' => null,
    'required' => false,
    'placeholder' => null,
    'options' => [],
    'value' => null,
    'maxItems' => 100,
    'clearable' => false,
    'position' => 'auto',
])
@php($wireModelAttribute = collect($attributes)->first(fn (string $value, string $attribute) => str_starts_with($attribute, 'wire:model')))

<div class="form-field" wire:ignore x-cloak>
    @isset($label)
        <label class="{{ $required ? 'label label-required' : 'label' }}" for="{{ $name }}">
            {{ $label }}
        </label>
    @endisset
    <div
        x-data="{
            multiple: false,
            @if ($wireModelAttribute)
            value: @entangle($wireModelAttribute),
            @else
            value: @js($value),
            @endif
            options: @js(collect($options)
                ->map(fn ($label, $value) => ['value' => $value, 'label' => $label])
                ->sortBy('label')
                ->values()
                ->toArray()),
            init() {
                this.$nextTick(() => {
                    let choices = new Choices(this.$refs.select, {
                        removeItemButton: {{ $clearable ? 'true' : 'false' }},
                        allowHTML: true,
                        searchEnabled: this.options.length >= 10,
                        searchResultLimit: 10,
                        placeholder: '{{ $placeholder }}',
                        position: '{{ $position }}',
                        searchPlaceholderValue: '{{ __('mailcoach - Search…') }}',
                    })

                    let refreshChoices = () => {
                        let selection = this.multiple ? this.value : [this.value]

                        choices.clearStore()
                        choices.setChoices(this.options.map(({ value, label }) => ({
                            value,
                            label,
                            selected: selection.includes(value),
                        })))
                    }

                    refreshChoices();

                    this.$refs.select.addEventListener('change', () => {
                        this.value = choices.getValue(true)
                    })

                    this.$watch('value', () => refreshChoices())
                    this.$watch('options', () => refreshChoices())
                })
            }
        }"
    >
        <select
            class="hidden"
            x-ref="select"
            id="{{ $name }}"
            {{ $required ? 'required' : '' }}
        ></select>
        <div class="select-arrow">
            <i class="fas fa-angle-down"></i>
        </div>
        <input type="hidden" name="{{ $name }}" :value="value">
    </div>
    @error($name)
        <p class="form-error" role="alert">{{ $message }}</p>
    @enderror
</div>
