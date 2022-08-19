<nav class="navigation-main" x-data="navigation">
    <div x-ref="background" class="navigation-dropdown-back"></div>

    <div class="px-6 flex flex-col md:flex-row md:items-center gap-x-4 gap-y-6">
        <div class="flex items-center">
            <a href="{{ route(config('mailcoach.redirect_home')) }}" class="flex items-center group">
                <span class="flex w-10 h-6 text-white transform group-hover:scale-90 transition-transform duration-150">
                    @include('mailcoach::app.layouts.partials.logoSvg')
                </span>
            </a>

            <button class="md:hidden text-white ml-auto text-2xl" x-on:click="show = !show"><i class="fa fa-bars"></i></button>
        </div>

        <div class="w-full flex flex-col md:flex-row md:items-center gap-y-8" x-show="show" x-transition>
            @include('mailcoach::app.layouts.partials.beforeFirstMenuItem')

            @foreach (app(\Spatie\Mailcoach\MainNavigation::class)->tree() as $index => $item)
                <div
                    class="navigation-dropdown-trigger group"
                    @if(count($item['children']) > 1)
                        x-on:mouseenter="open"
                        x-on:mouseleave="close"
                        x-on:touchstart="open"
                        x-on:click.outside="close"
                        x-on:keyup.escape.window="close"
                        x-on:resize.window.debounce="resize"
                    @endif
                >
                    <a x-on:click="select" class="inline-flex items-center py-2 md:px-3 lg:px-6 md:h-12" href="{{ $item['url'] }}">
                        <h3 class="group-hover:text-white {{ $item['active'] ? 'text-white' : 'text-white/80' }} uppercase md:text-xs font-bold tracking-wider">
                            {{ $item['title'] }}
                        </h3>
                    </a>
                    @if(count($item['children']) > 1)
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
