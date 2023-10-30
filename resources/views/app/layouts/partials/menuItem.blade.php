@if ($item->isForm)
    <form class="navigation-link" method="post" action="{{ $item->url }}">
        {{ csrf_field() }}
        <button type="submit" class="font-semibold">
            @if ($item->icon)
                <x-mailcoach::icon-label icon="fas fa-fw {{ $item->icon }}" :text="$item->label" />
            @else
                {{ $item->label }}
            @endif
        </button>
    </form>
@else
    <a x-on:click="select" class="navigation-link" href="{{ $item->url }}">
        @if ($item->icon)
            <x-mailcoach::icon-label x-on:click="select" icon="fas fa-fw {{ $item->icon }}" :text="$item->label" />
        @else
            <a x-on:click="select" class="navigation-link" href="{{ $item->url }}">{{ $item->label }}</a>
        @endif
    </a>
@endif
