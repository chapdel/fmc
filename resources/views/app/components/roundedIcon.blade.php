@php
    $typeCss = [
        'success' => 'from-green-500/70 to-green-700/90 text-white',
        'warning' => 'from-yellow-300/90 to-yellow-500/90 text-black',
        'error' => 'from-red-500/70 to-red-700/90 text-white',
        'info' => 'from-purple-500/70 to-purple-700/90 text-white',
        'neutral' => 'from-gray-700/70 to-gray-900/90 text-gray-100',
    ];

    if(!isset($type) || !array_key_exists($type, $typeCss)){
        $type = 'neutral';
    }

@endphp

<span class="
    w-4 h-4 rounded-full 
    inline-flex items-center justify-center 
    leading-none
    bg-gray-300 
    bg-gradient-to-b shadow-sm
    {{ $typeCss[$type] }}
    {{ $class ?? '' }}
">
    <i class="text-[8px] fa-fw {{ $icon ?? 'fas fa-info' }} "></i>
</span>
