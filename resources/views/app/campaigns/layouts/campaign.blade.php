@extends('mailcoach::app.layouts.app', ['title' => ($title ?? '')   . ' | ' .  $campaign->name, 'logoIcon' => 'far fa-envelope-open' ])

@section('up')
    <x-mailcoach::navigation-back :href="route('mailcoach.campaigns')" :label="__('Campaigns')"/>
@endsection

@section('nav')
     <x-mailcoach::navigation deep :title="$campaign->name">
        
        @if ($campaign->isAutomated() || $campaign->isSendingOrSent())
        <x-mailcoach::navigation-group icon="far fa-chart-line" :title="__('Performance')">
                <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.summary', $campaign)" data-dirty-warn>
                    {{ __('Summary') }}
                </x-mailcoach::navigation-item>
                <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.opens', $campaign)" data-dirty-warn>
                    {{ __('Opens') }}
                </x-mailcoach::navigation-item>
                <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.clicks', $campaign)" data-dirty-warn>
                    {{ __('Clicks') }}
                </x-mailcoach::navigation-item>
                <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.unsubscribes', $campaign)" data-dirty-warn>
                    {{ __('Unsubscribes') }}
                </x-mailcoach::navigation-item>
        </x-mailcoach::navigation-group>
        @endif

        <x-mailcoach::navigation-group icon="far fa-pencil-alt" :title="__('Composition')">
            <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.settings', $campaign)" data-dirty-warn>
                {{ __('Settings') }}
            </x-mailcoach::navigation-item>
            <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.content', $campaign)" data-dirty-warn>
                {{ __('Content') }}
            </x-mailcoach::navigation-item>
        </x-mailcoach::navigation-group>

        <x-mailcoach::navigation-group icon="far fa-paper-plane" :title="__('Delivery')">
            @if ($campaign->isSendingOrSent())
                <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.outbox', $campaign)" data-dirty-warn>
                    {{ __('Outbox') }}
                </x-mailcoach::navigation-item>
            @else
                <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.delivery', $campaign)" data-dirty-warn>
                    {{ __('Send') }}
                </x-mailcoach::navigation-item>
            @endif
        </x-mailcoach::navigation-group>

    </x-mailcoach::navigation>
@endsection

@section('main')    
        @yield('campaign')
@endsection
