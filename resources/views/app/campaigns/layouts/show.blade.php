@extends('mailcoach::app.layouts.app')

@section('content')
    <x-mailcoach::card>
        <x-slot name="nav">
            <x-mailcoach::card-nav :title="__('Campaigns')">
                <x-mailcoach::navigation-item :href="route('mailcoach.campaigns')">
                    {{ __('Overview') }}
                </x-mailcoach::navigation-item>
                <li>
                    <div class="card-nav-sub">
                        <h4 class="card-nav-sub-title">
                            {{ $campaign->name }}
                        </h4>
                        <ul>
                            @if ($campaign->isAutomated() || $campaign->isSendingOrSent())
                                <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.summary', $campaign)">
                                    <x-mailcoach::icon-label icon="far fa-chart-bar" :text="__('Performance')" invers />
                                </x-mailcoach::navigation-item>
                                
                            @endif

                            <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.settings', $campaign)" data-dirty-warn>
                                <x-mailcoach::icon-label icon="far fa-cog" :text="__('Settings')" invers />
                            </x-mailcoach::navigation-item>
                            <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.content', $campaign)" data-dirty-warn>
                                <x-mailcoach::icon-label icon="far fa-feather" :text="__('Content')" invers />
                            </x-mailcoach::navigation-item>
                            <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.delivery', $campaign)" data-dirty-warn>
                                <x-mailcoach::icon-label icon="far fa-paper-plane" :text="__('Send')" invers />
                            </x-mailcoach::navigation-item>
                        </ul>
                    </div>
                </li>
                <x-mailcoach::navigation-item :href="route('mailcoach.templates')">
                    {{ __('Templates') }}
                </x-mailcoach::navigation-item>

                
            </x-mailcoach::card-nav>
        </x-slot>
    
        @yield('campaign')
       
    </x-mailcoach::card>

@endsection
