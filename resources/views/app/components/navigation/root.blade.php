@props([
    'main' => false,
])
<div class="navigation relative z-50 flex items-start sticky top-0">
    <div class="flex flex-wrap lg:grid lg:grid-cols-1 gap-1 content-start sticky top-0 px-12 py-8">
        {{ $slot }}
    </div>
</div>
