@php use Spatie\Mailcoach\Mailcoach; @endphp
    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="referrer" content="always">

    <link rel="preconnect" href="https://fonts.gstatic.com">

    <title>{{ isset($title) ? "{$title} |" : '' }} {{ isset($originTitle) ? "{$originTitle} |" : '' }} Mailcoach</title>

    <script type="text/javascript">
        window.__ = function (key) {
            return {
                "Are you sure?": "{{ __mc('Are you sure?') }}",
                "Type to add tags": "{{ __mc('Type to add tags') }}",
                "No tags to choose from": "{{ __mc('No tags to choose from') }}",
                "Press to add": "{{ __mc('Press to add') }}",
                "Press to select": "{{ __mc('Press to select') }}",
            }[key];
        };
    </script>

    <style>[x-cloak] {
            display: none !important;
        }</style>
    <!-- Filament styles -->
    @filamentStyles
    @livewireStyles
    {!! Mailcoach::styles() !!}
    @include('mailcoach::app.layouts.partials.endHead')
    @stack('endHead')
</head>
<body class="flex flex-col min-h-screen text-gray-800 bg-indigo-900/5" x-data="{ confirmText: '', onConfirm: null }">
<script>/**/</script><!-- Empty script to prevent FOUC in Firefox -->

<div class="flex-grow">
    <header class="flex-none sticky top-0 z-20 w-full max-w-layout mx-auto px-0 md:px-16">
        <x-mailcoach::main-navigation/>
    </header>

    <main
        class="md:pt-10 px-6 md:px-16 relative flex-grow z-1 mx-auto w-full max-w-layout md:flex md:items-stretch md:gap-10">
        @isset($nav)
            <nav class="-mt-2 mb-4 md:my-0 flex-none md:w-[16rem]">
                {{ $nav }}
            </nav>
        @endisset

        <section class="flex-grow min-w-0 flex flex-col">
            @unless(isset($hideBreadcrumbs) && $hideBreadcrumbs)
                <nav class="mt-6 md:mt-0 flex-none">
                    @include('mailcoach::app.layouts.partials.breadcrumbs')
                </nav>
            @endunless

            <div class="flex-none flex items-center justify-between w-full">
                <div class="flex items-center w-full">
                    <h1 class="markup-h1 p-0 m-0 pb-2 truncate">
                        {{ $title ?? '' }}
                    </h1>
                </div>

                @if (($create ?? false) || ($createComponent ?? false))
                    <div class="buttons flex">
                        <x-mailcoach::button
                            x-on:click="$dispatch('open-modal', { id: 'create-{{ $create }}' })"
                            :label="$createText ?? __mc('Create ' . $create)"
                            class="mb-0"
                        />

                        <x-mailcoach::modal :title="$createText ?? __mc('Create ' . $create)"
                                            name="create-{{ $create }}">
                            @if ($createComponent ?? '')
                                @livewire($createComponent, $createData ?? [])
                            @else
                                @livewire('mailcoach::create-' . $create, $createData ?? [])
                            @endif
                        </x-mailcoach::modal>

                        {{ $afterCreate ?? '' }}
                    </div>
                @endif
            </div>

            <div>
                {{ $slot }}
            </div>
        </section>
    </main>

    <x-mailcoach::modal :title="__mc('Confirm')" name="confirm" :dismissable="true">
        <span x-text="confirmText"></span>

        <x-mailcoach::form-buttons>
            <x-mailcoach::button data-confirm type="button" x-on:click="onConfirm; $dispatch('close-modal', { id: 'confirm' })"
                                 :label=" __mc('Confirm')"/>
            <x-mailcoach::button-cancel x-on:click="$dispatch('close-modal', { id: 'confirm' })" :label=" __mc('Cancel')"/>
        </x-mailcoach::form-buttons>
    </x-mailcoach::modal>

    <x-mailcoach::modal :title="__mc('Confirm navigation')" name="dirty-warning">
        {{ __mc('There are unsaved changes. Are you sure you want to continue?') }}

        <x-mailcoach::form-buttons>
            <x-mailcoach::button type="button" x-on:click="$store.modals.onConfirm && $store.modals.onConfirm()"
                                 :label=" __mc('Confirm')"/>
            <x-mailcoach::button-cancel x-on:click="$dispatch('close-modal', { id: 'dirty-warning' })" :label=" __mc('Cancel')"/>
        </x-mailcoach::form-buttons>
    </x-mailcoach::modal>

    @stack('modals')
</div>

<footer class="mt-10">
    @include('mailcoach::app.layouts.partials.footer')
</footer>

<aside class="z-50 fixed bottom-4 left-4 w-64">
    @include('mailcoach::app.layouts.partials.startBody')

    @if ((new Spatie\Mailcoach\Domain\Shared\Support\License\License())->hasExpired())
        <div class="alert alert-warning text-sm shadow-lg">
            Your Mailcoach license has expired. <a class="underline" href="https://spatie.be/products/mailcoach">Renew
                your license</a> and benefit from fixes and new features.
        </div>
    @endif
</aside>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('modals', {
            openModals: [],
            onConfirm: null,
            init() {
                if (window.location.hash) {
                    this.openModals.push(window.location.hash.replace('#', ''));
                }
            },
            isOpen(id) {
                return this.openModals.includes(id);
            },
            open(id) {
                this.openModals.push(id);
                window.location.hash = id;
                Alpine.nextTick(() => {
                    const input = document.querySelector(`#modal-${id} input:not([type=hidden])`);
                    if (input) {
                        input.focus();
                        return;
                    }

                    const button = document.querySelector(`#modal-${id} [data-confirm]`);
                    if (button) button.focus();
                });
            },
            close(id) {
                this.openModals = this.openModals.filter(modal => modal !== id);
                history.pushState('', document.title, window.location.pathname + window.location.search);
            },
        });
    });
</script>
@filamentScripts
@livewireScriptConfig
{!! Mailcoach::scripts() !!}
@stack('scripts')
@livewire('notifications')
</body>
</html>
