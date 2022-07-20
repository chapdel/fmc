@props([
    'main' => false,
])
<div class="md:sticky md:top-14">
    <ul class="flex flex-col gap-2 md:gap-3 p-6 md:px-8 md:py-8 bg-gradient-to-b from-indigo-600/5 to-indigo-600/10 rounded-b-md md:rounded-md bg-clip-padding border border-indigo-700/10">
        {{ $slot }}
    </ul>
</div>
