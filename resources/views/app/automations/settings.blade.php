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
    <form
        class="form-grid"
        action="{{ route('mailcoach.automations.settings', $automation) }}"
        method="POST"
    >
        @csrf
        @method('PUT')

        <x-mailcoach::text-field :label="__('Name')" name="name" :value="$automation->name" required />

        @include('mailcoach::app.campaigns.partials.emailListFields', ['segmentable' => $automation])

        <x-mailcoach::text-field :label="__('Interval')" name="interval" :value="$automation->interval ?? '10 minutes'" required />

        <x-mailcoach::select-field
            :label="__('Trigger')"
            name="trigger"
            :options="$triggerOptions"
            placeholder="Select a trigger"
            data-conditional="trigger"
            required
            value="{{ $automation->trigger ? $automation->trigger::class : '' }}"
        />

        @foreach ($triggerOptions as $triggerClass => $triggerName)
            @if ($triggerClass::getComponent())
                <div data-conditional-trigger="{{ $triggerClass }}">
                    @livewire($triggerClass::getComponent(), [
                        'triggerClass' => $triggerClass,
                        'automation' => $automation,
                    ])
                </div>
            @endif
        @endforeach

        <div class="form-buttons">
            <button type="submit" class="button">
                <x-mailcoach::icon-label icon="fa-cog" :text="__('Save')" />
            </button>
        </div>
    </form>
@endsection
