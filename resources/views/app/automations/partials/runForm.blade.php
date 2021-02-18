<form
    class="form-grid"
    action="{{ route('mailcoach.automations.run', $automation) }}"
    method="POST"
>
    @csrf
    @method('PUT')

    <x-mailcoach::fieldset :legend="__('Interval')">
        <div class="flex items-end">
            <x-mailcoach::select-field
                name="interval"
                :value="$automation->interval ?? '1 minute'"
                :options="[
                    '1 minute' => 'Every minute',
                    '10 minutes' => 'Every 10 minutes',
                    '1 hour' => 'Hourly',
                    '1 day' => 'Daily',
                    '1 week' => 'Weekly',
                ]"
                required
            />

            <x-mailcoach::button class="ml-1" :label="__('Save')" />
        </div>
    </x-mailcoach::fieldset>

    <x-mailcoach::fieldset :legend="__('Run automation')">
        <div>
        @if ($automation->status === \Spatie\Mailcoach\Domain\Automation\Enums\AutomationStatus::STARTED)
            <button class="button" type="button" wire:click.prevent="pause">
                <span class="flex items-center">
                <x-mailcoach::rounded-icon type="warning" icon="fas fa-pause"/>
                <span class="ml-2">{{ __('Pause') }}</span>
                </span>
            </button>
        @else
            <button class="button" type="button" wire:click.prevent="start">
                <span class="flex items-center">
                <x-mailcoach::rounded-icon type="success" icon="fas fa-play"/>
                <span class="ml-2">{{ __('Start') }}</span>
                </span>
            </button>
        @endif
        </div>
    </x-mailcoach::fieldset>
</form>
