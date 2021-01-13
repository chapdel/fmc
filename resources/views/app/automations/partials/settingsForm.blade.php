<form
    class="form-grid"
    action="{{ route('mailcoach.automations.settings', $automation) }}"
    method="POST"
>
    @csrf
    @method('PUT')

    <x-mailcoach::text-field :label="__('Name')" name="name" :value="$automation->name" required />

    <x-mailcoach::select-field
        :label="__('Trigger')"
        name="trigger"
        :options="$triggerOptions"
        placeholder="Select a trigger"
        required
        value="{{ old('trigger', $automation->trigger ? $automation->trigger::class : '') }}"
        wire:change="setSelectedTrigger($event.target.value)"
    />

    @if ($selectedTrigger && $selectedTrigger::getComponent())
        @livewire($selectedTrigger::getComponent(), [
            'triggerClass' => $selectedTrigger,
            'automation' => $automation,
        ], key($selectedTrigger))
    @endif

    @include('mailcoach::app.campaigns.partials.emailListFields', ['segmentable' => $automation])

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
        <button type="submit" class="button">
            <x-mailcoach::icon-label icon="fa-cog" :text="__('Save')" />
        </button>

        @if ($automation->status === \Spatie\Mailcoach\Domain\Automation\Enums\AutomationStatus::STARTED)
            <button type="submit" class="button bg-orange-500" wire:click.prevent="pause">
                <x-mailcoach::icon-label icon="fa-pause" :text="__('Pause')" />
            </button>
        @else
            <button type="submit" class="button bg-green-500"  wire:click.prevent="start">
                <x-mailcoach::icon-label icon="fa-play" :text="__('Start')" />
            </button>
        @endif
    </div>
</form>
