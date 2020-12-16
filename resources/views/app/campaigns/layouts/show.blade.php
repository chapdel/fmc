@extends('mailcoach::app.layouts.app', [
    'title' => (isset($titlePrefix) ?  $titlePrefix . ' | ' : '') . $campaign->name
])

@section('header')
    <nav>
        <ul class="breadcrumbs">
            <li>
                <a href={{ route('mailcoach.campaigns') }}>
                    <span class="breadcrumb">{{ __('Campaigns') }}</span>
                </a>
            </li>
            @yield('breadcrumbs')
        </ul>
    </nav>
@endsection

@section('content')
    <nav class="tabs">
        <ul>
            @if ($campaign->isAutomated() || $campaign->isSendingOrSent())
                <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.summary', $campaign)">
                    <x-mailcoach::icon-label icon="fa-chart-area" :text="__('Summary')" />
                </x-mailcoach::navigation-item>
                <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.opens', $campaign)">
                    <x-mailcoach::icon-label icon="fa-envelope-open-text" :text="__('Opens')" />
                </x-mailcoach::navigation-item>
                <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.clicks', $campaign)">
                    <x-mailcoach::icon-label icon="fa-hand-pointer" :text="__('Clicks')" />
                </x-mailcoach::navigation-item>
                <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.unsubscribes', $campaign)">
                    <x-mailcoach::icon-label icon="fa-user-slash" :text="__('Unsubscribes')" />
                </x-mailcoach::navigation-item>
                <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.outbox', $campaign)">
                    <x-mailcoach::icon-label icon="fa-inbox" :text="__('Outbox')" />
                </x-mailcoach::navigation-item>
            @endif
            <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.settings', $campaign)" data-dirty-warn>
                <x-mailcoach::icon-label icon="fa-cog" :text="__('Settings')" />
            </x-mailcoach::navigation-item>
            <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.content', $campaign)" data-dirty-warn>
                <x-mailcoach::icon-label icon="fa-code" :text="__('Content')" />
            </x-mailcoach::navigation-item>
            <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.delivery', $campaign)" data-dirty-warn>
                <x-mailcoach::icon-label icon="fa-paper-plane" :text="__('Delivery')" />
            </x-mailcoach::navigation-item>
        </ul>
    </nav>

    <section class="card ">
        @yield('campaign')
    </section>
@endsection
