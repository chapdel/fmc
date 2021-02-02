<li class="{{ \Illuminate\Support\Str::startsWith(request()->url(), $href) ? 'active' : ''  }} dropdown" data-dropdown>
    <button class="icon-label dropdown-trigger" data-dropdown-trigger-hover>
        <i class="{{ $icon ?? '' }}"></i>
        <span class="icon-label-text font-semibold">{{ $label ?? '' }}</span>
    </button>
    <ul class="dropdown-list {{ isset($direction) ? 'dropdown-list-' . $direction : '' }} | hidden" data-dropdown-list>
        {{ $slot }}
    </ul>
</li>