@extends('mailcoach::app.automations.layouts.edit', [
    'automation' => $automation,
    'titlePrefix' => __('Actions'),
])

@section('breadcrumbs')
    <li>
        <a href="{{ route('mailcoach.automations.settings', $automation) }}">
            <span class="breadcrumb">{{ $automation->name }}</span>
        </a>
    </li>
    <li><span class="breadcrumb">{{ __('Actions') }}</span></li>
@endsection

@section('automation')
    <livewire:automation-actions :automation="$automation" :actions="$actions" />
@endsection

