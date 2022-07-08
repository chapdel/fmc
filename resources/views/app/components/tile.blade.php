@props([
    'cols' => 4,
    'icon' => null,
    'link' => null,
    'danger' => null,
])
<!-- col-span-1 col-span-2 col-span-3 col-span-4 col-span-5 col-span-6 col-span-7 col-span-8 col-span-9 col-span-10 col-span-11 col-span-12 -->
<div class="col-span-{{ $cols }} h-full {{ $attributes->get('class') }} {{ isset($link) ? 'dashboard-tile-hover' : ''}} {{ $danger ? 'dashboard-tile dashboard-tile-error' : 'dashboard-tile'}}" {{ $attributes->except('class') }}>
    @if ($icon)
        <i class="absolute -bottom-3 right-6 {{ $danger ? 'text-red-500/10' : 'text-blue-400/5' }} fas fa-{{ $icon }} text-[100px]"></i>
    @endif

    @if ($link) 
        <a href='{{$link}}' class="z-1 absolute inset-0">
        </a> 
    @endif

    <div class="z-10 h-full flex items-center">
        <div class="w-full ">
        {{ $slot }}
        </div>
    </div>

</div>
