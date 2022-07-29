<div class="text-center text-sm text-gray-400 flex items-center gap-x-1 justify-center" x-cloak x-data="{ key: 'CMD' }" x-init="platform = window.navigator.platform.indexOf('Mac') ? 'CMD' : 'CTRL' ">
    <x-mailcoach::icon-label class="-mr-1" icon="fas fa-lightbulb" /> <strong>ProTip!</strong> You can use <code><span x-text="key"></span>+K</code> to open the command palette
</div>
<div class="my-10 px-6 flex flex-wrap items-center justify-center text-xs text-gray-500">
    <a class="inline-block truncate max-w-[6rem]" href="https://mailcoach.app">
        Mailcoach {{ $versionInfo->getCurrentVersion() }}
    </a>
    <span>&nbsp;{{ __('mailcoach - by') }} <a class="" href="https://spatie.be">SPATIE</a></span>

    @if(Auth::check())
        <span class="mx-2">•</span>
        <a class="" href="https://mailcoach.app/docs" target="_blank">{{ __('mailcoach - Documentation') }}</a>

        <span class="mx-2">•</span>
        <a class="inline-block" href="{{ route('debug') }}">
            Debug
        </a>
        <span class="mx-2">•</span>
        <a class="inline-block" href="{{ route('export') }}">
            Export
        </a>
        <span class="mx-1">/</span>
        <a class="inline-block" href="{{ route('import') }}">
            Import
        </a>

        @if(! $versionInfo->isLatest())
            <a class="ml-4 inline-flex items-center" href="/">
                <i class="fas fa-horse-head mr-1"></i>
                {{ __('mailcoach - Upgrade available') }}
            </a>
        @endif
    @endif


    @if (! app()->environment('production') || config('app.debug'))
        <span class="ml-4 inline-flex items-center">
            <i class="text-red-500 far fa-wrench mr-1"></i>
            Env: {{ app()->environment() }} &mdash; Debug: {{ config('app.debug') ? 'true' : 'false' }}
        </span>
    @endif
</div>
