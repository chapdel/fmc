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
    <form
        class="form-grid"
        action="{{ route('mailcoach.automations.actions.store', $automation) }}"
        method="POST"
    >
        @csrf
        @method('POST')
        <livewire:automation-builder :automation="$automation" :actionData="['actions' => $actions]" />

        <div>
            <button type="submit" class="button">
                <x-mailcoach::icon-label icon="fa-save" :text="__('Save')" />
            </button>
        </div>
    </form>
@endsection

