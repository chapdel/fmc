<form
    class="form-grid"
    action="{{ route('mailcoach.automations.run', $automation) }}"
    method="POST"
>
    @csrf
    @method('PUT')

    <x-mailcoach::select-field
        :label="__('Interval')"
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

    <div class="form-buttons">
        <x-mailcoach::button :label="__('Save')" />

        @if ($automation->status === \Spatie\Mailcoach\Domain\Automation\Enums\AutomationStatus::STARTED)
            <button type="submit" class="button bg-orange-500" wire:click.prevent="pause">
                <x-mailcoach::icon-label icon="fas fa-pause" :text="__('Pause')" />
            </button>
        @else
            <button type="submit" class="button bg-green-500"  wire:click.prevent="start">
                <x-mailcoach::icon-label icon="fas fa-play" :text="__('Start')" />
            </button>
        @endif
    </div>
</form>
