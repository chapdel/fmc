@extends('mailcoach::app.automations.layouts.automation', [
    'automation' => $automation,
    'title' => __('Actions'),
])

@section('automation')
    <livewire:automation-actions :automation="$automation" :actions="$actions" />
@endsection

