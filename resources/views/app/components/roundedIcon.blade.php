@php
    $typeCss = [
        'success' => 'from-green-400 to-green-300',
        'warning' => 'from-orange-400 to-orange-300',
        'error' => 'from-red-500 to-red-400',
        'info' => 'from-blue-400 to-blue-300',
        'neutral' => 'from-gray-400 to-gray-300',
    ];

    if(!isset($type) || !array_key_exists($type, $typeCss)){
        $type = 'neutral';
    }

@endphp

<span class="
    w-4 h-4 rounded-full 
    inline-flex items-center justify-center 
    bg-gradient-to-t shadow-sm 
    leading-none
    {{ $typeCss[$type] }}
    {{ $class ?? '' }}
">
    <i style="font-size: 9px" class="text-white {{ $icon ?? 'fas fa-info' }} "></i>
</span>