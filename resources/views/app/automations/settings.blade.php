@extends('mailcoach::app.automations.layouts.automation', [
    'automation' => $automation,
    'title' => __('Settings'),
])

@section('automation')
    <livewire:automation-settings :automation="$automation" />
@endsection
