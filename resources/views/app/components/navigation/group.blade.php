<div class="{{ $class ?? '' }}">
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
    <div>
        {{ $slot }}
    </div>
</div>
