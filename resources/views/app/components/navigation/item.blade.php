@props([
    'href' => '',
    'active' => false,
])
<li class="{{ \Illuminate\Support\Str::startsWith($href, request()->url()) || $active ? 'active' : ''  }} {{ $attributes->get('class') }}" {{ $attributes->except('class') }}>
    <a href="{{ $href }}" @isset($dataDirtyWarn) data-dirty-warn @endisset>
        {{ $slot }}
    </a>
</li>
