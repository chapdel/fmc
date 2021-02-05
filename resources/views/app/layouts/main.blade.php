@extends('mailcoach::app.layouts.app')

@section('nav')
    <div class="flex justify-end px-8 pt-8">
        <a href="{{ route('mailcoach.home') }}">
            <span 
            class="group w-10 h-10 flex items-center justify-center bg-gradient-to-b from-blue-500 to-blue-600 text-white rounded-full">
                <span class="flex items-center justify-center w-6 h-6 transform group-hover:scale-90 transition-transform duration-150">
                    @include('mailcoach::app.layouts.partials.logoSvg')
                </span>
            </span>
        </a>
    </div>

    <x-mailcoach::navigation>

        @include('mailcoach::app.layouts.partials.beforeFirstMenuItem')

        @can("viewAny", \Spatie\Mailcoach\Domain\Campaign\Models\Campaign::class)
        <x-mailcoach::navigation-group icon="far fa-envelope-open" :title="__('Newsletter')">
            <x-mailcoach::navigation-item :href="route('mailcoach.campaigns')">
                {{ __('Campaigns') }}
            </x-mailcoach::navigation-item>
            <x-mailcoach::navigation-item :href="route('mailcoach.templates')">
                {{ __('Templates') }}
            </x-mailcoach::navigation-item>
        </x-mailcoach::navigation-group>
        @endcan

        <x-mailcoach::navigation-group icon="far fa-magic" :title="__('Drip')">
            <x-mailcoach::navigation-item :href="route('mailcoach.automations')">
                {{ __('Automations') }}
            </x-mailcoach::navigation-item>
            <x-mailcoach::navigation-item :href="route('mailcoach.automations')">
                {{ __('Emails') }}
            </x-mailcoach::navigation-item>
        </x-mailcoach::navigation-group>

        @can("viewAny", \Spatie\Mailcoach\Domain\Campaign\Models\EmailList::class)
        <x-mailcoach::navigation-group icon="far fa-address-book" :title="__('Audience')">
            <x-mailcoach::navigation-item :href="route('mailcoach.emailLists')">
                {{ __('Lists') }}
            </x-mailcoach::navigation-item>
        </x-mailcoach::navigation-group>
        @endcan

        <x-mailcoach::navigation-group icon="far fa-envelope-open" :title="__('Transactional')">
            <x-mailcoach::navigation-item :href="route('mailcoach.transactionalMails')">
                {{ __('Log') }}
            </x-mailcoach::navigation-item>
            <x-mailcoach::navigation-item :href="route('mailcoach.transactionalMails.templates')">
                {{ __('Templates') }}
            </x-mailcoach::navigation-item>
        </x-mailcoach::navigation-group>

        @include('mailcoach::app.layouts.partials.afterLastMenuItem')
       
        @include('mailcoach::app.layouts.partials.headerRight')
    </x-mailcoach::navigation>

@endsection

@section('content')
    @yield('main')
@endsection