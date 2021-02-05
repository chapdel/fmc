@extends('mailcoach::app.layouts.main', ['subTitle' => $automation->name ])

@section('nav')
     <x-mailcoach::navigation :title="$automation->name" :backHref="route('mailcoach.automations')" :backLabel="__('Automations')">
        <x-mailcoach::navigation-group icon="fas fa-magic" :title="__('Automation')">
            <x-mailcoach::navigation-item :href="route('mailcoach.automations.settings', $automation)" data-dirty-warn>
                {{ __('Settings') }}
            </x-mailcoach::navigation-item>
            <x-mailcoach::navigation-item :href="route('mailcoach.automations.actions', $automation)" data-dirty-warn>
                {{ __('Flow') }}
            </x-mailcoach::navigation-item>
            <x-mailcoach::navigation-item :href="route('mailcoach.automations.settings', $automation)" data-dirty-warn>
                {{ __('Run') }}
            </x-mailcoach::navigation-item>
        </x-mailcoach::navigation-group>

        <x-mailcoach::navigation-group icon="fas fa-chart-line" :title="__('Performance')">
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
