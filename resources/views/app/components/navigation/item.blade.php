@props([
    'href' => '',
    'active' => false,
])
<li class="navigation-item {{ \Illuminate\Support\Str::startsWith($href, request()->url()) || $active ? 'navigation-item-active' : ''  }} {{ $attributes->get('class') }}" {{ $attributes->except('class') }}>
    <a href="{{ $href }}">
        {{ $slot }}
    </a>
</li>
