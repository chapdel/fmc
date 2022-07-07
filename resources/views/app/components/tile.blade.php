@props([
    'cols' => 4,
    'icon' => null,
    'link' => null,
])
<!-- col-span-1 col-span-2 col-span-3 col-span-4 col-span-5 col-span-6 col-span-7 col-span-8 col-span-9 col-span-10 col-span-11 col-span-12 -->
<div class="col-span-{{ $cols }} h-full card px-5 py-3 {{ $attributes->get('class') }}" {{ $attributes->except('class') }}>
    {{-- <div class="flex justify-between items-center">
        @if ($icon)
            <i class="fas fa-{{ $icon }} opacity-50 text-2xl"></i>
        @endif
        {{ $link }}
    </div> --}}

    <div>
        {{ $slot }}
    </div>
</div>
