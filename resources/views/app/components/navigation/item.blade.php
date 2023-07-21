@props([
    'href' => '',
    'active' => false,
])
<li class="navigation-item {{ \Illuminate\Support\Str::startsWith($href, request()->url()) || $active ? 'navigation-item-active' : ''  }} {{ $attributes->get('class') }}" {{ $attributes->except('class') }}>
    <a href="{{ $href }}" @if($href) wire:navigate @endif @isset($dataDirtyWarn) data-dirty-warn @endisset>
        {{ $slot }}
    </a>
</li>
