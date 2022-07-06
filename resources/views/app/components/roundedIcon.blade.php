@php
    $typeCss = [
        'success' => 'from-green-400 to-green-500 text-white',
        'warning' => 'from-yellow-300 to-yellow-400 text-black',
        'error' => 'from-red-400 to-red-500 text-white',
        'info' => 'from-purple-400 to-purple-500 text-white',
        'neutral' => 'from-gray-600 to-gray-800 text-gray-100',
    ];

    if(!isset($type) || !array_key_exists($type, $typeCss)){
        $type = 'neutral';
    }

@endphp

<span class="
    w-4 h-4 rounded-full 
    inline-flex items-center justify-center 
    leading-none
    bg-gradient-to-b shadow-sm
    {{ $typeCss[$type] }}
    {{ $class ?? '' }}
">
    <i class="text-[8px] fa-fw {{ $icon ?? 'fas fa-info' }} "></i>
</span>
