@props([
    'title' => '',
])
<div class="md:sticky md:top-14">
    <div class="p-6 md:px-8 md:py-8 bg-gradient-to-b from-indigo-600/5 to-indigo-600/10 rounded-b-md md:rounded-md bg-clip-padding border border-indigo-700/10">
        @if($title)
        <h2 class="mb-6 font-extrabold text-xs uppercase tracking-wider truncate">{{ $title ?? '' }}</h2>
        @endif
        <ul class="flex flex-col gap-2 md:gap-3">
            {{ $slot }}
        </ul>
    </div>
</div>
