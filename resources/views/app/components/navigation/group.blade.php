@props([
    'title' => null,
    'href' => null,
    'active' => false,
])
@php
$isActive = ($active || \Illuminate\Support\Str::startsWith($href, request()->url()));
@endphp
<div class="{{ $attributes->get('class') }}" {{ $attributes->except('class') }}>
    @if($title)
        @php
            $maxLength = 22;
            $partLength = floor(($maxLength - 1)/2);
            $titleTruncated = strlen($title) > $maxLength ?
                substr($title, 0, $partLength ) . '…' . substr($title, -$partLength )
                : $title;
        @endphp
        <li class="py-1 text-sm font-semibold {{ $isActive ? 'active' : ''  }}">
            <a href="{{ $href ?? '#' }}">
                {{ $titleTruncated ?? '' }}
            </a>
        </li>
    @endif
    <div class="flex items-center md:items-start gap-x-4 md:flex-col list-none @if($title) ml-4 @endif">
        {{ $slot }}
    </div>
</div>
