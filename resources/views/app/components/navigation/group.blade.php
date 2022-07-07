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
            @isset($href)
            <a href="{{ $href }}">
                {{ $titleTruncated ?? '' }}
            </a>
            @else
            <span>
                {{ $titleTruncated ?? '' }}
            </span>
            @endif
        </li>
    @endif
    <ul class="mt-3 flex items-center md:items-start gap-x-4 gap-y-3 md:flex-col @if($title) nav-group @endif">
        {{ $slot }}
    </ul>
</div>
