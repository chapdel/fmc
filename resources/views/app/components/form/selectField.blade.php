@props([
    'label' => null,
    'name' => null,
    'required' => false,
    'placeholder' => null,
    'options' => [],
    'value' => null,
    'maxItems' => 100,
    'clearable' => false,
])
@php($wireModelAttribute = collect($attributes)->first(fn (string $value, string $attribute) => str_starts_with($attribute, 'wire:model')))

<div class="form-field">
    @isset($label)
        <label class="{{ $required ? 'label label-required' : 'label' }}" for="{{ $name }}">
            {{ $label }}
        </label>
    @endisset
    <div
        wire:ignore
        x-data="{
            @if ($wireModelAttribute)
            value: @entangle($wireModelAttribute),
            @else
            value: @js($value),
            @endif
            options: @js(collect($options)
                ->map(fn ($label, $value) => ['value' => $value, 'label' => $label])
                ->sortBy('label')
                ->values()
                ->toArray()
            ),
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
                            tagTextProp: 'label',
                            mode: 'select',
                            placeholder: '{{ $placeholder }}',
                            dropdown: {
                                mapValueTo: 'label',
                                maxItems: {{ $maxItems }},
                            }
                        });
                    }

                    refreshTagify()

                    this.$refs.select.addEventListener('change', () => {
                        this.value = tagify.value[0] ? tagify.value[0].value : '';
                    })

                    this.$watch('value', () => refreshTagify())
                    this.$watch('options', () => refreshTagify())
                })
            }
        }"
        class="{{ $clearable ? 'tagify--clearable' : '' }}"
    >
        <input
            x-ref="select"
            x-model="value"
            type="hidden"
            id="{{ $name }}"
            {{ $required ? 'required' : '' }}
            {!! $attributes->except(['value', 'options', 'required', 'name', 'maxItems']) ?? '' !!}
            class="input"
        />
        <input type="hidden" name="{{ $name }}" :value="value">
    </div>
    @error($name)
        <p class="form-error" role="alert">{{ $message }}</p>
    @enderror
</div>
