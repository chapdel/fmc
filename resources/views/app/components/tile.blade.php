@props([
    'cols' => 4,
    'icon' => null,
    'link' => null,
    'danger' => null,
])
<!-- md:col-span-1 md:col-span-2 md:col-span-3 md:col-span-4 md:col-span-5 md:col-span-6 md:col-span-7 md:col-span-8 md:col-span-9 md:col-span-10 md:col-span-11 md:col-span-12 -->
<div class="md:col-span-{{ $cols }} h-full {{ $attributes->get('class') }} {{ isset($link) ? 'dashboard-tile-hover' : ''}} {{ $danger ? 'dashboard-tile dashboard-tile-error' : 'dashboard-tile'}}" {{ $attributes->except('class') }}>
    @if ($icon)
        <i class="absolute -bottom-3 right-6 {{ $danger ? 'text-red-400/10' : 'text-blue-400/5' }} fas fa-{{ $icon }} text-[100px]"></i>
    @endif

    @if ($link) 
        <a href='{{$link}}' class="z-1 absolute inset-0">
        </a> 
    @endif

    <div class="pointer-events-none z-10 h-full flex items-center">
        <div class="w-full ">
            {{ $slot }}
        </div>
    </div>

</div>
