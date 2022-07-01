@props([
    'title' => null,
    'href' => null,
    'active' => false,
])
@php
$isActive = ($active || \Illuminate\Support\Str::startsWith($href, request()->url()));
@endphp
<div {{ $attributes }}>
    @if($title)
        @php
            $maxLength = 22;
            $partLength = floor(($maxLength - 1)/2);
            $titleTruncated = strlen($title) > $maxLength ?
                substr($title, 0, $partLength ) . '…' . substr($title, -$partLength )
                : $title;
        @endphp
        <li class="nav-item {{ $isActive ? 'nav-item-active' : ''  }}">
            <a href="{{ $href ?? '#' }}">
                {{ $titleTruncated ?? '' }}
            </a>
        </li>
    @endif
    <ul class="mt-2 flex items-center md:items-start gap-x-4 gap-y-3 md:flex-col @if($title) nav-group @endif">
        {{ $slot }}
    </ul>
</div>
