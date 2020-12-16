@extends('mailcoach::app.layouts.app', [
    'title' => (isset($titlePrefix) ?  $titlePrefix . ' | ' : '') . $automation->name
])

@section('header')
    <nav>
        <ul class="breadcrumbs">
            <li>
                <a href={{ route('mailcoach.automations') }}>
                    <span class="breadcrumb">{{ __('Automations') }}</span>
                </a>
            </li>
            @yield('breadcrumbs')
        </ul>
    </nav>
@endsection

@section('content')
    <nav class="tabs">
        <ul>
            <x-navigation-item :href="route('mailcoach.automations.settings', $automation)" data-dirty-warn>
                <x-icon-label icon="fa-cog" :text="__('Settings')" />
            </x-navigation-item>
            <x-navigation-item :href="route('mailcoach.automations.actions', $automation)" data-dirty-warn>
                <x-icon-label icon="fa-bolt" :text="__('Actions')" />
            </x-navigation-item>
        </ul>
    </nav>
    <section class="card">
        @yield('automation')
    </section>
@endsection
