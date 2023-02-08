@props([
    'active' => false,
    'attribute' => false,
    'value' => false,
    'current' => '',
    'activeClass' => '',
])
<li>
    <a href="#" wire:click.prevent="setFilter('{{ $attribute }}', '{{ $value }}')" {{ $attributes->except('class') }} class="{{ $current === $value ? 'filter-active ' . $activeClass : '' }} {{ $attributes->get('class') }} ">
        {{ $slot }}
    </a>
</li>
