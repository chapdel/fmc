<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="referrer" content="always">

        <link rel="preconnect" href="https://fonts.gstatic.com">

        <title>{{ isset($title) ? "{$title} |" : '' }} {{ isset($originTitle) ? "{$originTitle} |" : '' }} Mailcoach</title>

        {!! \Spatie\Mailcoach\Mailcoach::styles() !!}

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

        @include('mailcoach::app.layouts.partials.endHead')
        @stack('endHead')
    </head>
    <body class="flex flex-col min-h-screen text-gray-800 bg-indigo-900/5" x-data="{ confirmText: '', onConfirm: null }">
        <script>/**/</script><!-- Empty script to prevent FOUC in Firefox -->
        
        <header class="flex-none sticky top-0 z-20 w-full max-w-layout mx-auto px-16">
            <x-mailcoach::main-navigation />
        </header>

        <main id="swup" class="pt-10 relative flex-grow z-1 mx-auto w-full max-w-layout px-16 md:flex md:items-stretch md:gap-10">
            @isset($nav)
                <nav class="flex-none md:w-[16rem]">
                    {{ $nav }}
                </nav>
            @endisset
            
            <section class="flex-grow min-w-0 flex flex-col">
                @unless(isset($hideBreadcrumbs) && $hideBreadcrumbs)
                    <nav class="flex-none">
                        @include('mailcoach::app.layouts.partials.breadcrumbs')
                    </nav>
                @endunless
                
                <div class="flex-none flex">
                    <h1 class="mt-1 markup-h1 truncate">
                        {{ $title ?? '' }}
                    </h1>
                </div>
                
                <div>
                   {{ $slot }} 
                </div>
            </section>
        </main>
        
        <footer class="mt-10">
            @include('mailcoach::app.layouts.partials.footer')
        </footer>

        <aside class="z-50 fixed bottom-4 left-4 w-64">
            @include('mailcoach::app.layouts.partials.startBody')

            @if ((new Spatie\Mailcoach\Domain\Shared\Support\License\License())->hasExpired())
                <div class="alert alert-warning text-sm shadow-lg">
                    Your Mailcoach license has expired. <a class="underline" href="https://spatie.be/products/mailcoach">Renew your license</a> and benefit from fixes and new features.
                </div>
            @endif

            @include('mailcoach::app.layouts.partials.flash')
        </aside>

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

        {!! \Livewire\Livewire::scripts() !!}
        @livewire('livewire-ui-spotlight')
        {!! \Spatie\Mailcoach\Mailcoach::scripts() !!}
    </body>
</html>
