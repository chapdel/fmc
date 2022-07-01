@props([
    'href' => '',
    'active' => false,
])
<li class="nav-item {{ \Illuminate\Support\Str::startsWith($href, request()->url()) || $active ? 'nav-item-active' : ''  }} {{ $attributes->get('class') }}" {{ $attributes->except('class') }}>
    <a href="{{ $href }}" @isset($dataDirtyWarn) data-dirty-warn @endisset>
        {{ $slot }}
    </a>
</li>
