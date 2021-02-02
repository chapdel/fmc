@extends('mailcoach::app.layouts.app')

@section('content')
    <x-mailcoach::card>
        <x-slot name="nav">
            <x-slot name="nav">
            <x-mailcoach::card-nav :title="__('Campaigns')">
                <x-mailcoach::navigation-item :href="route('mailcoach.campaigns')">
                    {{ __('Campaigns') }}
                </x-mailcoach::navigation-item>
                <x-mailcoach::navigation-item :href="route('mailcoach.templates')">
                    {{ __('Templates') }}
                </x-mailcoach::navigation-item>
            </x-mailcoach::card-nav>
        </x-slot>
    
        @yield('campaigns')
       
    </x-mailcoach::card>
@endsection