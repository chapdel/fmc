<nav class="navigation-main relative" x-data="navigation">
    <div x-ref="background" class="pointer-events-none select-none navigation-main-background w-16 h-16 absolute bg-white rounded shadow-xl flex justify-center opacity-0 z-40" style="transition: all 0.3s, opacity 0.1s, transform 0.2s">
        <div class="absolute w-4 h-4 bg-white -translate-y-1/2 rotate-45"></div>
    </div>

    <div class="flex items-center gap-x-8">
        <div class="navigation-logo pl-2 pr-6 self-center flex items-center justify-between">
            <a class="pl-3" data-no-swup href="{{ route('mailcoach.home') }}">
                <span
                    class="group w-10 h-10 flex items-center justify-center bg-gradient-to-b from-blue-500 to-blue-600 text-white rounded-full">
                    <span class="flex items-center justify-center w-6 h-6 transform group-hover:scale-90 transition-transform duration-150">
                        @include('mailcoach::app.layouts.partials.logoSvg')
                    </span>
                </span>
            </a>
        </div>

        @include('mailcoach::app.layouts.partials.beforeFirstMenuItem')

        @foreach (app(\Spatie\Mailcoach\MainNavigation::class)->tree() as $index => $item)
            <div
                class="navigation-item group relative py-8 px-4 cursor-pointer"
                @if(count($item['children']))
                    x-on:mouseenter="open"
                    x-on:mouseleave="close"
                    x-on:touchstart="open"
                    x-on:click.outside="close"
                    x-on:keyup.escape.window="close"
                @endif
            >
                <h3 class="text-blue-100 group-hover:text-white uppercase text-xs font-semibold tracking-wider"><a href="{{ $item['url'] }}">{{ $item['title'] }}</a></h3>
                @if (count($item['children']))
                    <div class="navigation-dropdown hidden opacity-0 absolute left-1/2 -translate-x-1/2 translate-y-4 overflow-hidden py-3 rounded transition-opacity duration-200 z-50 min-w-32 will-change-auto">
                        @foreach ($item['children'] as $child)
                            <a class="navigation-link block w-full py-2 hover:bg-blue-100 px-4" href="{{ $child['url'] }}">{{ $child['title'] }}</a>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach

        @include('mailcoach::app.layouts.partials.headerRight')
    </div>
</nav>
