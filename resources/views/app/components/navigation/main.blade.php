<nav class="navigation-main sticky top-0 border-b border-black/10 shadow-lg bg-gradient-to-r from-blue-800 to-blue-900" x-data="navigation">
    <div x-ref="background" class="pointer-events-none select-none w-16 h-16 absolute bg-white rounded shadow-xl flex justify-center opacity-0 z-40" style="transition: all 0.3s, opacity 0.1s, transform 0.2s">
        <div class="absolute w-3 h-3 bg-white -translate-y-1/2 rotate-45"></div>
    </div>

    <div class="max-w-layout mx-auto px-10 md:flex md:items-center gap-4">
        <div class="flex">
            <div class="navigation-logo flex items-center justify-between">
                <a href="{{ route(config('mailcoach.redirect_home')) }}">
                    <span
                        class="group w-10 h-10 flex items-center justify-center bg-gradient-to-b from-blue-500 to-blue-600 text-white rounded-full">
                        <span class="flex items-center justify-center w-6 h-6 transform group-hover:scale-90 transition-transform duration-150">
                            @include('mailcoach::app.layouts.partials.logoSvg')
                        </span>
                    </span>
                </a>
            </div>

            <button class="md:hidden text-white ml-auto text-3xl" x-on:click="show = !show"><i class="fa fa-bars"></i></button>
        </div>

        <div class="w-full flex flex-col md:flex-row md:items-center" x-show="show" x-transition x-cloak>
            @include('mailcoach::app.layouts.partials.beforeFirstMenuItem')

            @foreach (app(\Spatie\Mailcoach\MainNavigation::class)->tree() as $index => $item)
                <div
                    class="navigation-item group relative cursor-pointer"
                    @if(count($item['children']) && $item['title'] !== __('mailcoach - Audience'))
                        x-on:mouseenter="open"
                        x-on:mouseleave="close"
                        x-on:touchstart="open"
                        x-on:click.outside="close"
                        x-on:keyup.escape.window="close"
                    @endif
                >
                    <a class="inline-flex items-center px-4 md:px-6 h-16" href="{{ $item['url'] }}">
                        <h3 class="group-hover:text-white {{ $item['active'] ? 'text-white' : 'text-blue-200' }} uppercase text-xs font-semibold tracking-wider">
                            {{ $item['title'] }}
                        </h3>
                    </a>
                    @if (count($item['children']) && $item['title'] !== __('mailcoach - Audience'))
                        <!-- md:block md:opacity-100 -->
                        <div class="navigation-dropdown md:hidden md:opacity-0 md:absolute md:left-1/2 md:-translate-x-1/2 overflow-hidden py-3 rounded transition-opacity duration-200 z-50 min-w-32 will-change-auto">
                            @foreach ($item['children'] as $child)
                                <a class="navigation-link block w-full py-2 md:hover:bg-blue-100 px-4 text-blue-200 md:text-black" href="{{ $child['url'] }}">{{ $child['title'] }}</a>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach

            @include('mailcoach::app.layouts.partials.headerRight')
        </div>
    </div>
</nav>
