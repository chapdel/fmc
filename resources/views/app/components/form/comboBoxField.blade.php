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

<!-- Include these scripts somewhere on the page: -->
<script defer src="https://unpkg.com/@alpinejs/ui@3.13.0-beta.0/dist/cdn.min.js"></script>

@php
    // @todo optional: add support for disabled options
    $options = collect($options)
                ->map(fn ($label, $value) => ['value' => $value, 'label' => $label, 'disabled' => false])
                ->when($sort, fn ($collection) => $collection->sortBy('label'))
                ->values()
                ->toArray();
@endphp

<div form-field {{ $class }}>
    @if($label)
        <label class="{{ $required ? 'label label-required' : 'label' }}" for="{{ $name }}">
            {{ $label }}

            @if ($help)
                <i class="ml-1 text-purple-500 opacity-75 cursor-pointer fas fa-question-circle" x-data x-tooltip="{{ $help }}"></i>
            @endif
        </label>
    @endif

    <div
        x-data="{
        query: '',
        selected: null,
        options: @js($options),
        get filteredOptions() {
            return this.query === ''
                ? this.options
                : this.options.filter((option) => {
                    return option.label.toLowerCase().includes(this.query.toLowerCase())
                })
        },
        remove(option) {
            this.selected = this.selected.filter((i) => i !== option)
        }
    }"
        class="form-field {{ $class }}"
    >
        <div x-combobox x-model="selected" :multiple="$multiple">
            <div class="mt-1 relative rounded-md focus-within:ring-2 focus-within:ring-blue-500">
                <div class="flex items-center justify-between gap-2 w-full bg-white pl-5 pr-3 py-2.5 rounded-md shadow">
                    <input
                        x-combobox:input
                        :display-value="options => options.label"
                        @change="query = $event.target.value;"
                        class="border-none p-0 focus:outline-none focus:ring-0"
                        placeholder="Search..."
                    />
                    <button x-combobox:button class="absolute inset-y-0 right-0 flex items-center pr-2">
                        <!-- Heroicons up/down -->
                        <svg class="shrink-0 w-5 h-5 text-gray-500" viewBox="0 0 20 20" fill="none" stroke="currentColor"><path d="M7 7l3-3 3 3m0 6l-3 3-3-3" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    </button>
                </div>

                <div x-combobox:options x-cloak class="absolute left-0 max-w-xs w-full max-h-60 mt-2 z-10 origin-top-right overflow-auto bg-white border border-gray-200 rounded-md shadow-md outline-none" x-transition.out.opacity>
                    <ul class="divide-y divide-gray-100">
                        <template
                            x-for="option in filteredOptions"
                            :key="option.value"
                            hidden
                        >
                            <li
                                x-combobox:option
                                :value="option"
                                :disabled="option.disabled"
                                :class="{
                                'bg-cyan-500/10 text-gray-900': $comboboxOption.isActive,
                                'text-gray-600': ! $comboboxOption.isActive,
                                'opacity-50 cursor-not-allowed': $comboboxOption.isDisabled,
                            }"
                                class="flex items-center cursor-default justify-between gap-2 w-full px-4 py-2 text-sm"
                            >
                                <span x-text="option.label"></span>

                                <span x-show="$comboboxOption.isSelected" class="text-cyan-600 font-bold">&check;</span>
                            </li>
                        </template>
                    </ul>

                    <p x-show="filteredOptions.length == 0" class="px-4 py-2 text-sm text-gray-600">No results.</p>
                </div>
            </div>
        </div>
    </div>
</div>
