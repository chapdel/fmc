<div class="navigation-group">
    <div class="flex justify-end">
        <h3 class="truncate">
            <span class="icon-label icon-label-invers">
                @isset($icon)
                <i class="fa-fw {{ $icon }}"></i>
                @endisset
                <span class="icon-label-text">
                    {{ $title ?? '' }}
                </span>
            </span>
        </h3>
    </div>
    <ul>
        {{ $slot }}
    </ul>
</div>