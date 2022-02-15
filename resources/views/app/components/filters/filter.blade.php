<li>
    <a href="{{ $href }}" class="{{ $active() ? 'filter-active' : '' }}" data-turbo-preserve-scroll>
        {{ $slot }}
    </a>
</li>
