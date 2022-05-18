<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="referrer" content="always">

        <link rel="preconnect" href="https://fonts.gstatic.com">

        <title>{{ isset($title) ? "{$title} |" : '' }} {{ isset($originTitle) ? "{$originTitle} |" : '' }} Mailcoach</title>

        <link rel="stylesheet" href="{{ asset('vendor/mailcoach/app.css') }}?t={{ app(\Spatie\Mailcoach\Domain\Shared\Support\Version::class)->getHashedFullVersion() }}">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.11.0/css/all.css">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
        @foreach (\Spatie\Mailcoach\Mailcoach::availableEditorStyles() as $editor => $styles)
            @continue(! in_array($editor, [
                config('mailcoach.content_editor'),
                config('mailcoach.template_editor'),
            ]))
            @foreach ($styles as $style)
                <link rel="stylesheet" href="{{ $style }}">
            @endforeach
        @endforeach

        <meta name="turbo-cache-control" content="no-preview">

        {!! \Livewire\Livewire::styles() !!}
        <script type="text/javascript">
            window.__ = function (key) {
                return {
                    "Are you sure?": "{{ __('mailcoach - Are you sure?') }}",
                    "Type to add tags": "{{ __('mailcoach - Type to add tags') }}",
                    "No tags to choose from": "{{ __('mailcoach - No tags to choose from') }}",
                    "Press to add": "{{ __('mailcoach - Press to add') }}",
                    "Press to select": "{{ __('mailcoach - Press to select') }}",
                }[key];
            };
        </script>

        <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js" defer></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-zoom/1.2.1/chartjs-plugin-zoom.min.js" defer></script>

        @include('mailcoach::app.layouts.partials.endHead')
        @stack('endHead')
    </head>
    <body class="bg-gray-100" x-data="{ confirmText: '', onConfirm: null }">
        <script>/**/</script><!-- Empty script to prevent FOUC in Firefox -->


        <main id="swup">
            <div class="mx-auto grid w-full max-w-layout min-h-screen p-6 z-auto" style="grid-template-rows: auto auto 2fr auto">
                <aside>
                    @include('mailcoach::app.layouts.partials.startBody')

                    @if ((new Spatie\Mailcoach\Domain\Shared\Support\License\License())->hasExpired())
                        <div class="mb-6 alert alert-warning text-sm shadow-lg">
                            Your Mailcoach license has expired. <a class="underline" href="https://spatie.be/products/mailcoach">Renew your license</a> and benefit from fixes and new features.
                        </div>
                    @endif

                    @include('mailcoach::app.layouts.partials.flash')
                </aside>

                <header class="">
                    <nav class="shadow-lg rounded bg-gradient-to-r from-blue-800 via-blue-800 to-blue-900 w-full">
                        <x-mailcoach::main-navigation />
                    </nav>

                    <div class="h-12 flex items-center gap-x-4 px-4 text-gray-700 text-sm">
                        <a class="font-semibold" href="{{ route(config('mailcoach.redirect_home')) }}"><i class="fa fa-home"></i></a>
                        <span>&gt;</span>
                        @foreach (app(\Spatie\Mailcoach\MainNavigation::class)->breadcrumbs() as $breadcrumb)
                            <a class="font-semibold" href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a>
                            @if (! $loop->last)
                                <span>&gt;</span>
                            @endif
                        @endforeach
                        @if (isset($title) && $title !== $breadcrumb['title'])
                            <span>&gt;</span>
                            <span>
                                @if (($originTitle ?? '') && $originTitle !== $title)
                                    <span class="">{{ $originTitle }}</span>
                                    <span>&mdash;</span>
                                @endif
                                <span class="">{{ $title }}</span>
                            </span>
                        @endif
                    </div>
                </header>

                <div>
                    <div class="h-full card {{ $nav ?? '' ? 'card-split': '' }}">
                        @isset($nav)
                            <nav class="bg-blue-50/70">
                                {{ $nav }}
                            </nav>
                        @endisset

                        <main class="card-main">
                            <h1 class="markup-h1">
                                @isset($originTitle)
                                    <div class="markup-h1-sub">
                                        @isset($originHref)
                                            <a class="text-blue-500" href="{{ $originHref }}">{{ $originTitle }}</a>
                                        @else
                                            {{ $originTitle }}
                                        @endif
                                    </div>
                                @endif
                                {{ $title ?? '' }}
                            </h1>
                            {{ $slot }}
                        </main>
                    </div>
                </div>

                <footer class="px-6 pt-6">
                    @include('mailcoach::app.layouts.partials.footer')
                </footer>
            </div>

            <x-mailcoach::modal :title="__('mailcoach - Confirm')" name="confirm">
                <span x-text="confirmText"></span>

                <div class="form-buttons">
                    <x-mailcoach::button type="button" x-on:click="onConfirm; $store.modals.close('confirm')" :label=" __('mailcoach - Confirm')" />
                    <x-mailcoach::button-cancel  x-on:click="$store.modals.close('confirm')" :label=" __('mailcoach - Cancel')" />
                </div>
            </x-mailcoach::modal>

            <x-mailcoach::modal :title="__('mailcoach - Confirm navigation')" name="dirty-warning">
                {{ __('mailcoach - There are unsaved changes. Are you sure you want to continue?') }}

                <div class="form-buttons">
                    <x-mailcoach::button type="button" x-on:click="$store.modals.onConfirm && $store.modals.onConfirm()" :label=" __('mailcoach - Confirm')" />
                    <x-mailcoach::button-cancel  x-on:click="$store.modals.close('dirty-warning')" :label=" __('mailcoach - Cancel')" />
                </div>
            </x-mailcoach::modal>

            @stack('modals')
        </main>

        {!! \Livewire\Livewire::scripts() !!}
        @livewire('livewire-ui-spotlight')
        @foreach (\Spatie\Mailcoach\Mailcoach::availableEditorScripts() as $editor => $scripts)
            @continue(! in_array($editor, [
                config('mailcoach.content_editor'),
                config('mailcoach.template_editor'),
            ]))
            @foreach ($scripts as $script)
                <script type="text/javascript" src="{{ $script }}" defer></script>
            @endforeach
        @endforeach
        <script type="text/javascript" src="{{ asset('vendor/mailcoach/app.js') }}?t={{ app(\Spatie\Mailcoach\Domain\Shared\Support\Version::class)->getHashedFullVersion() }}" defer></script>
    </body>
</html>
