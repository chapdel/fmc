@extends('mailcoach::app.automations.layouts.automation', [
    'automation' => $automation,
    'title' => __('Settings'),
])

@section('automation')
    <h1 class="markup-h1">{{ __('Settings') }}</h1>
    <livewire:automation-settings :automation="$automation" />
@endsection
