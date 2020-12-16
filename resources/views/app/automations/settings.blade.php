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

        <x-mailcoach::select-field
            :label="__('Trigger')"
            name="trigger"
            :options="$triggerOptions"
            placeholder="Select a trigger"
            data-conditional="trigger"
            value="{{ $triggerOptions->search(optional($automation->trigger)->getName()) }}"
        />

        @foreach ($triggerOptions as $index => $triggerName)
            <div data-conditional-trigger="{{ $index }}">
                @if (config('mailcoach.automation.triggers')[$index] && config('mailcoach.automation.triggers')[$index]::getComponent())
                    <div class="mt-6">
                        @livewire(config('mailcoach.automation.triggers')[$index]::getComponent(), [
                            'triggerClass' => $selectedActionClass,
                        ], key(config('mailcoach.automation.triggers')[$index]))
                    </div>
                @endif
            </div>
        @endforeach

        <div class="form-buttons">
            <button type="submit" class="button">
                <x-mailcoach::icon-label icon="fa-cog" :text="__('Save')" />
            </button>
        </div>
    </form>
@endsection
