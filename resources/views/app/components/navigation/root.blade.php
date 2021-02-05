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
            <div class="mb-3 flex justify-end">
                @isset($backHref)
                    <div class="text-right">
                        <a href="{{ $backHref }}" class="text-blue-200 text-opacity-50 text-sm font-semibold hover:text-white">
                            <span class="icon-label">
                                <i class="text-blue-500 far fa-angle-left"></i>
                                <span class="icon-label-text">
                                    {{ $backLabel ?? '' }}
                                </span>
                            </span>
                        </a>
                    </div>
                @endisset

                <div class="ml-3">
                    <a href="{{ route('mailcoach.home') }}">
                        <span 
                        class="group w-7 h-7 flex items-center justify-center bg-gradient-to-b from-blue-500 to-blue-600 text-white rounded-full">
                            <span class="flex items-center justify-center w-4 h-4 transform group-hover:scale-90 transition-transform duration-150">
                                @include('mailcoach::app.layouts.partials.logoSvg')
                            </span>
                        </span>
                    </a>
                </div>
        </div>

        <h2 class="uppercase tracking-wider text-right text-xs text-blue-100 font-semibold">
            {{ $titleTruncated }}
        </h2>
    </div>
@endisset

<div class="navigation">
    {{ $slot }}
</div>