@extends('mailcoach::app.layouts.app')

@section('nav')
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

        <x-mailcoach::navigation-group icon="far fa-magic" :title="__('Automation')">
            <x-mailcoach::navigation-item :href="route('mailcoach.automations')">
                {{ __('Flows') }}
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
       
    </x-mailcoach::navigation>

    <div class="mt-auto pt-8 flex justify-end">
        @include('mailcoach::app.layouts.partials.headerRight')
    </div>
@endsection
