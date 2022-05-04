@props([
    'active' => false,
    'attribute' => false,
    'value' => false,
    'filter' => [],
])
<li>
    <a href="#" wire:click.prevent="setFilter('{{ $attribute }}', '{{ $value }}')" {{ $attributes->except('class') }} class="{{ ($filter[$attribute] ?? '') === $value ? 'filter-active' : '' }} {{ $attributes->get('class') }} ">
        {{ $slot }}
    </a>
</li>
