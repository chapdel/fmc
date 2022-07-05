<div class="my-6 flex flex-wrap items-center justify-center text-xs text-indigo-900/50">
    <a class=" inline-block truncate" style="max-width: 12rem" href="https://mailcoach.app">
        Mailcoach {{ $versionInfo->getCurrentVersion() }}
    </a>
    <span>&nbsp;{{ __('mailcoach - by') }} <a class="" href="https://spatie.be">SPATIE</a></span>

    @if(Auth::check())
        <span class="mx-2">•</span>
        <a class="" href="https://spatie.be/docs/laravel-mailcoach" target="_blank">{{ __('mailcoach - Documentation') }}</a>

        <span class="mx-2">•</span>
        <a class=" inline-block truncate" style="max-width: 12rem" href="{{ route('debug') }}">
            Debug
        </a>
        <span class="mx-2">•</span>
        <a class=" inline-block truncate" style="max-width: 12rem" href="{{ route('export') }}">
            Export
        </a>
        <span class="mx-1">/</span>
        <a class=" inline-block truncate" style="max-width: 12rem" href="{{ route('import') }}">
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
