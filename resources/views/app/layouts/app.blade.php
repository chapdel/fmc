<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="referrer" content="always">

        <title>{{ isset($title) ? "{$title} | Mailcoach" : 'Mailcoach' }}</title>

        <link rel="stylesheet" href="{{ asset('vendor/mailcoach/app.css') }}?t={{ app(\Spatie\Mailcoach\Domain\Shared\Support\Version::class)->getHashedFullVersion() }}">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.11.0/css/all.css">
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css">

        <meta name="turbolinks-cache-control" content="no-preview">

        {!! \Livewire\Livewire::styles() !!}
        <script type="text/javascript">
            window.__ = function (key) {
                return {
                    "Are you sure?": "{{ __('Are you sure?') }}",
                    "Type to add tags": "{{ __('Type to add tags') }}",
                    "No tags to choose from": "{{ __('No tags to choose from') }}",
                    "Press to add": "{{ __('Press to add') }}",
                    "Press to select": "{{ __('Press to select') }}",
                }[key];
            };
        </script>
        <script type="text/javascript" src="{{ asset('vendor/mailcoach/app.js') }}?t={{ app(\Spatie\Mailcoach\Domain\Shared\Support\Version::class)->getHashedFullVersion() }}" defer></script>

        @include('mailcoach::app.layouts.partials.endHead')
        @stack('endHead')
    </head>
    <body class="bg-gray-100">
        <script>/**/</script><!-- Empty script to prevent FOUC in Firefox -->
        
        <div class="layout">
            <aside class="layout-flash">
                @include('mailcoach::app.layouts.partials.startBody')
            </aside>
            
            
            <header class="layout-header">
                @include('mailcoach::app.layouts.partials.navigation')

                {{-- <div>
                    @yield('header')
                </div> --}}
            </header>

            <nav class="layout-header-right">
                @include('mailcoach::app.layouts.partials.headerRight')
            </nav>

            <main class="layout-main">
                @include('mailcoach::app.layouts.partials.flash')
                @yield('content')
            </main>

            <footer class="layout-footer">
                @include('mailcoach::app.layouts.partials.footer')
            </footer>
        </div>

        <x-mailcoach::modal :title="__('Confirm')" name="confirm">
            <span data-confirm-modal-text>{{ __('Are you sure?') }}</span>

            <div class="form-buttons">
                <button type="button" class="button" data-modal-confirm>
                    {{ __('Confirm') }}
                </button>
                <button type="button" class="button-cancel" data-modal-dismiss>
                    {{ __('Cancel') }}
                </button>
            </div>
        </x-mailcoach::modal>

        <x-mailcoach::modal :title="__('Confirm navigation')" name="dirty-warning">
            {{ __('There are unsaved changes. Are you sure you want to continue?') }}

            <div class="form-buttons">
                <button type="button" class="button" data-modal-confirm>
                    {{ __('Continue') }}
                </button>
                <button type="button" class="button-cancel" data-modal-dismiss>
                    {{ __('Cancel') }}
                </button>
            </div>
        </x-mailcoach::modal>

        @stack('modals')
        {!! \Livewire\Livewire::scripts() !!}
        <script src="https://cdn.jsdelivr.net/gh/livewire/turbolinks@v0.1.x/dist/livewire-turbolinks.js" data-turbolinks-eval="false"></script>
    </body>
</html>
