@props([
    'href' => '',
    'active' => false,
])
<li class="py-1 text-sm font-semibold {{ \Illuminate\Support\Str::startsWith($href, request()->url()) || $active ? 'active' : ''  }} {{ $attributes->get('class') }}" {{ $attributes->except('class') }}>
    <a href="{{ $href }}" @isset($dataDirtyWarn) data-dirty-warn @endisset>
        {{ $slot }}
    </a>
</li>
