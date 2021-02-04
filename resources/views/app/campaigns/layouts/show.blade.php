@extends('mailcoach::app.layouts.app', ['title' => $title   . ' | ' .  $campaign->name, 'logoIcon' => 'far fa-envelope-open' ])

@section('up')
    <x-mailcoach::navigation-back :href="route('mailcoach.campaigns')" :label="__('Campaigns')"/>
@endsection

@section('nav')
     <x-mailcoach::navigation deep :title="$campaign->name">
        <x-mailcoach::navigation-group icon="far fa-envelope-open" :title="__('Campaign')">
            @if ($campaign->isAutomated() || $campaign->isSendingOrSent())
            <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.summary', $campaign)" data-dirty-warn>
                {{ __('Performance') }}
            </x-mailcoach::navigation-item>
            @endif
            <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.settings', $campaign)" data-dirty-warn>
                {{ __('Settings') }}
            </x-mailcoach::navigation-item>
            <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.content', $campaign)" data-dirty-warn>
                {{ __('Content') }}
            </x-mailcoach::navigation-item>
            <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.delivery', $campaign)" data-dirty-warn>
                {{ __('Send') }}
            </x-mailcoach::navigation-item>
        </x-mailcoach::navigation-group>

        <x-mailcoach::navigation-group icon="far fa-chart-line" :title="__('Performance')">
            @if ($campaign->isAutomated() || $campaign->isSendingOrSent())
                <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.summary', $campaign)">
                    {{ __('Performance') }}
                </x-mailcoach::navigation-item>  
            @endif
                <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.summary', $campaign)">
                    {{ __('Summary') }}
                </x-mailcoach::navigation-item>
                <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.summary', $campaign)">
                    {{ __('Opens') }}
                </x-mailcoach::navigation-item>
                <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.summary', $campaign)">
                    {{ __('Clicks') }}
                </x-mailcoach::navigation-item>
                <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.summary', $campaign)">
                    {{ __('Unsubscribes') }}
                </x-mailcoach::navigation-item>
        </x-mailcoach::navigation-group>
    </x-mailcoach::navigation>
@endsection

@section('main')    
        @yield('campaign')
@endsection
