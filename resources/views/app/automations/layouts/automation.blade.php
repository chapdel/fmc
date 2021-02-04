@extends('mailcoach::app.layouts.app', ['title' => ($title ?? '')   . ' | ' .  $automation->name, 'logoIcon' => 'far fa-magic' ])

@section('up')
    <x-mailcoach::navigation-back :href="route('mailcoach.automations')" :label="__('Automations')"/>
@endsection

@section('nav')
     <x-mailcoach::navigation deep :title="$automation->name">
        <x-mailcoach::navigation-group icon="far fa-project-diagram" :title="__('Composition')">
            <x-mailcoach::navigation-item :href="route('mailcoach.automations.settings', $automation)" data-dirty-warn>
                {{ __('Settings') }}
            </x-mailcoach::navigation-item>
            <x-mailcoach::navigation-item :href="route('mailcoach.automations.actions', $automation)" data-dirty-warn>
                {{ __('Flow') }}
            </x-mailcoach::navigation-item>
        </x-mailcoach::navigation-group>

        <x-mailcoach::navigation-group icon="far fa-paper-plane" :title="__('Delivery')">
            <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.outbox', $automation)" data-dirty-warn>
                {{ __('Outbox') }}??
            </x-mailcoach::navigation-item>
            <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.delivery', $automation)" data-dirty-warn>
                {{ __('Run') }}
            </x-mailcoach::navigation-item>
        </x-mailcoach::navigation-group>

        <x-mailcoach::navigation-group icon="far fa-chart-line" :title="__('Performance')">
                <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.summary', $automation)" data-dirty-warn>
                    {{ __('Summary') }}
                </x-mailcoach::navigation-item>
                <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.opens', $automation)" data-dirty-warn>
                    {{ __('Opens') }}
                </x-mailcoach::navigation-item>
                <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.clicks', $automation)" data-dirty-warn>
                    {{ __('Clicks') }}
                </x-mailcoach::navigation-item>
                <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.unsubscribes', $automation)" data-dirty-warn>
                    {{ __('Unsubscribes') }}
                </x-mailcoach::navigation-item>
        </x-mailcoach::navigation-group>
    </x-mailcoach::navigation>
@endsection     

@section('main')
    @yield('automation')
@endsection
