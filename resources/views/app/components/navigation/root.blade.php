@props([
    'main' => false,
])
<div class="h-full">
    <ul class="sticky top-14 flex flex-col gap-3 mb-10 px-10 py-8 md:pb-24 bg-gradient-to-b from-indigo-600/5 to-indigo-600/10 rounded-md bg-clip-padding border border-indigo-700/10">
        {{ $slot }}
    </ul>
</div>
