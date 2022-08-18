@props([
    'label' => '',
    'required' => false,
    'name' => '',
    'multiple' => true,
    'tags' => [],
    'value' => [],
    'allowCreate' => false,
])

@php($wireModelAttribute = collect($attributes)->first(fn ($value, $attribute) => str_starts_with($attribute, 'wire:model')))
<div
    wire:ignore
    x-data="{
        multiple: {{ $multiple ? 'true' : 'false' }},
        @if ($wireModelAttribute)
        value: @entangle($wireModelAttribute),
        @else
        value: @js($value),
        @endif
        options: @js(array_values($tags)),
        init() {
            this.$nextTick(() => {
                let choices = new Choices(this.$refs.select, {
                    removeItemButton: true,
                    allowHTML: true,
                    searchEnabled: this.options.length >= 10,
                    searchResultLimit: 10,
                    searchPlaceholderValue: '{{ __('mailcoach - Search…') }}',
                })

                let refreshChoices = () => {
                    let selection = this.multiple ? this.value : [this.value]

                    choices.clearStore()
                    choices.setChoices(this.options.map((tag) => ({
                        value: tag,
                        label: tag,
                        selected: selection.includes(tag),
                    })))
                }

                refreshChoices();

                this.$refs.select.addEventListener('change', () => {
                    this.value = choices.getValue(true)
                    $wire.emit('tags-updated', this.value);
                    $wire.emit('tags-updated-{{ $name }}', this.value);
                })

                this.$watch('value', () => refreshChoices())
                this.$watch('options', () => refreshChoices())
            })
        }
    }"
    class="form-field choices-multiple"
>
    @isset($label)
        <label class="{{ $required ? 'label label-required' : 'label' }}" for="{{ $name }}">
            {{ $label }}
        </label>
    @endisset
    <select
        x-ref="select"
        id="{{ $name }}"
        {{ $required ? 'required' : '' }}
        {{ $multiple ? 'multiple' : '' }}
        {!! $attributes->except(['value', 'tags', 'required', 'multiple', 'name', 'allowCreate']) ?? '' !!}
        class="input"
    ></select>
    <template x-for="tag in value">
        <input type="hidden" name="{{ $name }}[]" :value="tag">
    </template>
    @error($name)
        <p class="form-error" role="alert">{{ $message }}</p>
    @enderror
</div>
