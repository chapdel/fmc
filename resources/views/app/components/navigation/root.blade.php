@props([
    'main' => false,
])
<div class="navigation relative z-50 flex items-start sticky top-0">
    <div class="flex flex-wrap lg:grid lg:grid-cols-1 gap-6 content-start sticky top-0 px-12 py-8">
        @isset($title)
            @php
                $maxLength = 24;
                $partLength = floor(($maxLength - 1)/2);
                $titleTruncated = strlen($title) > $maxLength ?
                    substr($title, 0, $partLength ) . '…' . substr($title, -$partLength )
                    : $title;
            @endphp
            <h2 class="
                col-span-2 sm:col-span-4 lg:col-span-1
                -mx-2 px-2 py-4 border-b border-black border-opacity-10 uppercase tracking-wider lg:text-right text-xs text-blue-500 font-semibold whitespace-nowrap">
                <span class="hidden lg:block">{{ $titleTruncated }}</span>
                <span class="lg:hidden block">{{ $title }}</span>
            </h2>
        @endisset

        {{ $slot }}
    </div>
</div>
