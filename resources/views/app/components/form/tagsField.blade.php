@props([
    'label' => '',
    'required' => false,
    'name' => '',
    'multiple' => false,
    'tags' => [],
    'value' => [],
    'allowCreate' => false,
])

@php($wireModelAttribute = $attributes->first(fn ($attribute) => str_starts_with($attribute, 'wire:model')))

<div
    wire:ignore
    x-data="{
        multiple: {{ $multiple ? 'true' : 'false' }},
        @if ($wireModelAttribute)
        value: @entangle($wireModelAttribute),
        @else
        value: @js($value),
        @endif
        options: @js($tags),
        init() {
            this.$nextTick(() => {
                let tagify;

                let refreshTagify = () => {
                    if (tagify) {
                        tagify.destroy();
                    }

                    tagify = new Tagify(this.$refs.select, {
                        originalInputValueFormat: valuesArr => valuesArr.map(item => item.value),
                        whitelist: this.options,
                        maxTags: {{ $multiple ? 'Infinity' : '1'  }},
                        dropdown: {
                            closeOnSelect: false,
                            enabled : 0,
                        },
                        editTags: false,
                    });
                }

                refreshTagify()

                this.$refs.select.addEventListener('change', () => {
                    @if ($multiple)
                    this.value = tagify.value.map(v => v.value);
                    @else
                    this.value = tagify.value[0] ? tagify.value[0].value : '';
                    @endif
                    $wire.emit('tags-updated', this.value);
                    $wire.emit('tags-updated-{{ $name }}', this.value);
                })

                this.$watch('value', () => refreshTagify())
                this.$watch('options', () => refreshTagify())
            })
        }
    }"
    class="form-field"
>
    @isset($label)
        <label class="{{ $required ? 'label label-required' : 'label' }}" for="{{ $name }}">
            {{ $label }}
        </label>
    @endisset
    <input
        x-ref="select"
        x-model="value"
        :multiple="multiple"
        type="text"
        id="{{ $name }}"
        {{ $required ? 'required' : '' }}
        {{ $multiple ? 'multiple' : '' }}
        {!! $attributes->except(['value', 'tags', 'required', 'multiple', 'name', 'allowCreate']) ?? '' !!}
        class="input"
    />
    <template x-for="tag in value">
        <input type="hidden" name="{{ $name }}[]" :value="tag">
    </template>
    @error($name)
        <p class="form-error" role="alert">{{ $message }}</p>
    @enderror
</div>
