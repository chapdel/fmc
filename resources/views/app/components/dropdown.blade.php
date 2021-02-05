<div class="dropdown" data-dropdown>
    <button type="button" class="hover:text-blue-500" data-dropdown-trigger>
        @if(isset($trigger))
            {{ $trigger }}
        @else
            <i class="fas fa-ellipsis-v | dropdown-trigger-rotate"></i>
        @endif
    </button>
    <div class="dropdown-list {{ isset($direction) ? 'dropdown-list-' . $direction : '' }} | hidden" data-dropdown-list>
        {{ $slot }}
    </div>
</div>