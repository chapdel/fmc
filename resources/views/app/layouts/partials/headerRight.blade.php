<div
    class="navigation-dropdown-trigger group"
    x-on:mouseenter="open"
    x-on:mouseleave="close"
    x-on:touchstart="open"
    x-on:click.outside="close"
    x-on:keyup.escape.window="close"
>
    <div class="group inline-flex items-center h-12">
        <div class="relative rounded-full w-8 h-8 shadow-md">
            <img class="rounded-full w-8 h-8 opacity-90 group-hover:opacity-100" src="https://www.gravatar.com/avatar/{{ md5(auth()->guard(config('mailcoach.guard'))->user()->email) }}?d=mp" alt="{{ auth()->user()->name }}">
            <div class="absolute inset-0 rounded-full bg-gradient-to-t from-transparent to-white/30"></div>
            <div class="absolute inset-0 rounded-full border-2 border-t-white/30 border-l-white/30 border-r-black/10 border-b-black/10"></div>
        </div>
    </div>
    <div class="navigation-dropdown md:hidden md:opacity-0">
        @foreach (\Spatie\Mailcoach\Mailcoach::$userMenuItems['before'] as $item)
            @include('mailcoach::app.layouts.partials.menuItem')
        @endforeach
        @can ('viewMailcoach')
        <a x-on:click="select" class="navigation-link" href="{{ route('general-settings') }}">
            <x-mailcoach::icon-label icon="fas fa-fw fa-cog" :text="__mc('Configuration')" />
        </a>
        @endcan
        @foreach (\Spatie\Mailcoach\Mailcoach::$userMenuItems['after'] as $item)
            @include('mailcoach::app.layouts.partials.menuItem')
        @endforeach
    </div>
</div>
