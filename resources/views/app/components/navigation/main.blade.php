<nav class="navigation-main" x-data="navigation">
    <div x-ref="background" class="navigation-dropdown-back"></div>

    <div class="px-6 flex flex-col md:flex-row md:items-center gap-x-4 gap-y-6">
        <div class="flex">
            <a href="{{ route(config('mailcoach.redirect_home')) }}">
                <span
                    class="group w-8 h-8 flex items-center justify-center bg-gradient-to-t from-indigo-900 to-blue-900 shadow-md text-white rounded-full">
                    <span class="flex items-center justify-center w-5 h-5 transform group-hover:scale-90 transition-transform duration-150">
                        @include('mailcoach::app.layouts.partials.logoSvg')
                    </span>
                </span>
            </a>

            <button class="md:hidden text-white ml-auto text-2xl" x-on:click="show = !show"><i class="fa fa-bars"></i></button>
        </div>

        <div class="w-full flex flex-col md:flex-row md:items-center gap-y-8" x-show="show" x-transition x-cloak>
            @include('mailcoach::app.layouts.partials.beforeFirstMenuItem')

            @foreach (app(\Spatie\Mailcoach\MainNavigation::class)->tree() as $index => $item)
                <div
                    class="navigation-dropdown-trigger group"
                    @if(count($item['children']) && $item['children'][0]['url'] !== url()->current())
                        x-on:mouseenter="open"
                        x-on:mouseleave="close"
                        x-on:touchstart="open"
                        x-on:click.outside="close"
                        x-on:keyup.escape.window="close"
                        x-on:resize.window.debounce="resize"
                    @endif
                >
                    <a x-on:click="select" class="inline-flex items-center py-2 md:px-6 md:h-12" href="{{ $item['url'] }}">
                        <h3 class="group-hover:text-white {{ $item['active'] ? 'text-white' : 'text-white/80' }} uppercase md:text-xs font-bold tracking-wider">
                            {{ $item['title'] }}
                        </h3>
                    </a>
                    @if (count($item['children']) && $item['title'] !== __('mailcoach - Audience'))
                        <!-- md:block md:opacity-100 -->
                        <div class="navigation-dropdown md:hidden md:opacity-0">
                            @foreach ($item['children'] as $child)
                                <a x-on:click="select" class="navigation-link" href="{{ $child['url'] }}">{{ $child['title'] }}</a>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach

            @include('mailcoach::app.layouts.partials.afterLastMenuItem')

            <div class="md:ml-auto">
                @include('mailcoach::app.layouts.partials.headerRight')
            </div>
        </div>
    </div>
</nav>
