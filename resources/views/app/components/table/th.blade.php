<th {{ $attributes }}>
    @if($sortable)
        <a href="{{ $href }}" data-turbo-action="replace" data-turbo-preserve-scroll>
            {{ $slot }}
            @if($isSortedAsc())
                <i class="fas fa-arrow-up text-gray-400"></i>
            @elseif($isSortedDesc())
                <i class="fas fa-arrow-down text-gray-400"></i>
            @endif
        </a>
    @else
        {{ $slot }}
    @endisset
</th>
