<span class="inline-flex items-center">
    <span class="
        w-3.5 h-3.5 rounded-full 
        inline-flex items-center justify-center 
        bg-gradient-to-t shadow-sm 
        leading-none
        {{ $test ? 
            'from-green-400 to-green-300' :
            (isset($warn) ? 'from-orange-400 to-orange-300' : 'from-red-500 to-red-400')
        }}
    ">
        <i style="font-size: 9px" class="text-white {{ $test ? 
        'fa-fw fas fa-check' :
        (isset($warn) ? 'fas fa-exclamation' : 'fas fa-times')
    }} "></i>
    </span>

    @if(isset($label))
        <span class="ml-2">
            {{ $label }}
        </span>
    @endisset
</span>
