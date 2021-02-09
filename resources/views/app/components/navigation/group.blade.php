
<div class="navigation-group">
    @isset($title)
        @php
            $maxLength = 22;
            $partLength = floor(($maxLength - 1)/2);
            $titleTruncated = strlen($title) > $maxLength ? 
                substr($title, 0, $partLength ) . '…' . substr($title, -$partLength )
                : $title;
        @endphp
        <div class="flex justify-end">
            <h3 class="truncate">
                <span class="icon-label icon-label-invers">
                    @isset($icon)
                    <i class="fa-fw {{ $icon }}"></i>
                    @endisset
                    <span class="icon-label-text">
                        {{ $titleTruncated ?? '' }}
                    </span>
                </span>
            </h3>
        </div>
    @endisset
    <ul>
        {{ $slot }}
    </ul>
</div>