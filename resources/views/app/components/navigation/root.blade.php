@isset($title)   
    @php
        $fullTitle = $title;
        $maxLength = 24;
        $partLength = floor(($maxLength - 1)/2);
        $titleTruncated = strlen($fullTitle) > $maxLength ? 
            substr($fullTitle, 0, $partLength ) . '…' . substr($fullTitle, -$partLength )
            : $fullTitle;
    @endphp

    <div class="rounded-tl px-8 py-8 bg-blue-900">
        <div class="flex items-center justify-between">
            @isset($backHref)
                <a href="{{ $backHref }}" class="text-blue-100 text-sm font-semibold hover:text-white">
                    <span class="icon-label">
                        <i class="text-blue-500 fas fa-angle-left"></i>
                        <span class="icon-label-text">
                            {{ $backLabel ?? '' }}
                        </span>
                    </span>
                </a>
            @endisset

            <a class="ml-3" href="{{ route('mailcoach.home') }}">
                <span 
                class="group w-10 h-10 flex items-center justify-center bg-gradient-to-b from-blue-500 to-blue-600 text-white rounded-full">
                    <span class="flex items-center justify-center w-6 h-6 transform group-hover:scale-90 transition-transform duration-150">
                        @include('mailcoach::app.layouts.partials.logoSvg')
                    </span>
                </span>
            </a>
        </div>

        <h2 class="hidden uppercase tracking-wider text-right text-xs text-blue-100 font-semibold">
            {{ $titleTruncated }}
        </h2>
    </div>
@endisset

<div class="navigation">
    {{ $slot }}
</div>