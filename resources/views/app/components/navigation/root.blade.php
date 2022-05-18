@props([
    'main' => false,
])
<div class="relative z-50 flex items-start sticky top-0" x-data="{ shown: false }" x-init="shown = window.innerWidth > 768">
    <div class="navigation" :class="[shown ? 'navigation-shown' : '']">
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
