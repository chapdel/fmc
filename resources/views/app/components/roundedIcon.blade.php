@php
    $typeCss = [
        'success' => 'from-green-500/80 to-green-700/90 text-white',
        'warning' => 'from-yellow-500/90 to-orange-600/90 text-white',
        'error' => 'from-red-500/90 to-red-700/90 text-white',
        'info' => 'from-purple-500/70 to-purple-700/90 text-white',
        'neutral' => 'from-gray-700/70 to-gray-900/90 text-gray-100',
    ];

    if(!isset($type) || !array_key_exists($type, $typeCss)){
        $type = 'neutral';
    }

    $sizeCss = [
        'sm' => 'w-4 h-4 text-[8px] ',
        'md' => 'h-6 w-6 text-[10px] ',
        'lg' => 'h-8 w-8 text-[12px] ',
    ];

    if(!isset($size) || !array_key_exists($size, $sizeCss)){
        $size = 'sm';
    }

@endphp

<span class="
    rounded-full 
    inline-flex items-center justify-center 
    leading-none
    bg-gray-300 
    bg-gradient-to-b shadow-sm
    {{ $sizeCss[$size] }}
    {{ $typeCss[$type] }}
    {{ $class ?? '' }}
">
    <i class="fa-fw {{ $icon ?? 'fas fa-info' }} "></i>
</span>
