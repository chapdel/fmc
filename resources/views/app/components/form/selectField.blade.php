@props([
    'label' => null,
    'help' => null,
    'name' => null,
    'required' => false,
    'placeholder' => null,
    'options' => [],
    'value' => null,
    'maxItems' => 100,
    'clearable' => false,
    'position' => 'auto',
    'multiple' => false,
    'sort' => true,
    'class' => '',
])
@php($wireModelAttribute = collect($attributes)->first(fn (string $value, string $attribute) => str_starts_with($attribute, 'wire:model')))

<div class="form-field {{ $multiple ? 'choices-multiple' : '' }} {{ $class }}" x-cloak>
    @if($label)
        <label class="{{ $required ? 'label label-required' : 'label' }}" for="{{ $name }}">
            {{ $label }}

            @if ($help)
                <i class="ml-1 text-purple-500 opacity-75 cursor-pointer fas fa-question-circle" x-data x-tooltip="{{ $help }}"></i>
            @endif
        </label>
    @endif
    <div
        wire:ignore
        x-data="{
            multiple: {{ $multiple ? 'true' : 'false' }},
            @if ($wireModelAttribute)
            value: @entangle($wireModelAttribute).live,
            @else
            value: @js($value),
            @endif
            options: @js(collect($options)
                ->map(fn ($label, $value) => ['value' => $value, 'label' => $label])
                ->when($sort, fn ($collection) => $collection->sortBy('label'))
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
                        shouldSort: false,
                        searchPlaceholderValue: '{{ __mc('Search…') }}',
                    })

                    let refreshChoices = () => {
                        let selection = this.multiple ? this.value : [this.value]

                        if (this.multiple && !Array.isArray(selection)) {
                            selection = [selection];
                        }

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
            {{ $required ? 'required' : '' }}
            {{ $multiple ? 'multiple' : '' }}
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
