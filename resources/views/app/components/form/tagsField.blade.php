@props([
    'label' => '',
    'required' => false,
    'name' => '',
    'multiple' => false,
    'tags' => [],
    'value' => [],
    'allowCreate' => false,
])
<div
    wire:ignore
    x-data="{
        multiple: true,
        value: @js($value),
        options: @js($tags),
        init() {
            this.$nextTick(() => {
                let tagify;

                let refreshTagify = () => {
                    let selection = this.multiple ? this.value : [this.value]

                    if (tagify) {
                        tagify.destroy();
                    }

                    tagify = new Tagify(this.$refs.select, {
                        originalInputValueFormat: valuesArr => valuesArr.map(item => item.value),
                        whitelist: this.options,
                        dropdown: {
                            closeOnSelect: false,
                            enabled : 0,
                        },
                        editTags: false,
                    });
                }

                refreshTagify()

                this.$refs.select.addEventListener('change', () => {
                    this.value = tagify.value.map(v => v.value);
                    $wire.emit('tags-updated', this.value);
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
