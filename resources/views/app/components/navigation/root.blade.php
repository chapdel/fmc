

<div class="navigation {{ isset($deep) ? 'navigation-deep' : '' }}">
    @isset($title)   
        @php
            $fullTitle = $title;
            $maxLength = 24;
            $partLength = floor(($maxLength - 1)/2);
            $titleTruncated = strlen($fullTitle) > $maxLength ? 
                substr($fullTitle, 0, $partLength ) . '…' . substr($fullTitle, -$partLength )
                : $fullTitle;
        @endphp

        <h2>
            {{ $titleTruncated }}
        </h2>
    @endisset
    {{ $slot }}
</div>