@extends('mailcoach::app.automations.layouts.automation', [
    'automation' => $automation,
    'title' => __('Actions'),
])

@section('automation')
    <h1 class="markup-h1">{{ __('Actions') }}</h1>
    <livewire:automation-actions :automation="$automation" :actions="$actions" />
@endsection

