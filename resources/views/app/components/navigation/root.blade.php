@props([
    'main' => false,
])
<div class="h-full">
    <ul class="sticky top-14 flex flex-col gap-3 px-10 pt-8 pb-24 bg-gradient-to-b from-indigo-600/5 to-indigo-600/10 rounded-md">
        {{ $slot }}
    </ul>
</div>
