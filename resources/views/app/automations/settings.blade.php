@extends('mailcoach::app.automations.layouts.edit', [
    'automation' => $automation,
    'titlePrefix' => __('Settings'),
])

@section('breadcrumbs')
    <li>
        <a href="{{ route('mailcoach.automations.settings', $automation) }}">
            <span class="breadcrumb">{{ $automation->name }}</span>
        </a>
    </li>
    <li><span class="breadcrumb">{{ __('Settings') }}</span></li>
@endsection

@section('automation')
    <livewire:automation-settings :automation="$automation" />
@endsection
