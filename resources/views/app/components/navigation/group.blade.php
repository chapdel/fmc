<div class="navigation-group {{ $class ?? '' }}"
     x-data="navigation"
     @if(\Illuminate\Support\Str::contains((string) $slot, 'Campaigns'))
     x-init="moveBackground()"
     @endif
     x-on:mouseenter="moveBackground()"
     x-on:mouseleave="onMouseOut()"
>
    @isset($title)
        @php
            $maxLength = 22;
            $partLength = floor(($maxLength - 1)/2);
            $titleTruncated = strlen($title) > $maxLength ?
                substr($title, 0, $partLength ) . '…' . substr($title, -$partLength )
                : $title;
        @endphp
        <h3 class="truncate">
            {{ $titleTruncated ?? '' }}
        </h3>
    @endisset
    <div class="navigation-group-content" x-ref="content">
        {{ $slot }}
    </div>
</div>
