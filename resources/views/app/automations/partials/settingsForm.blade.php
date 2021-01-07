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
        required
        value="{{ old('trigger', $automation->trigger ? $automation->trigger::class : '') }}"
        wire:change="setSelectedTrigger($event.target.value)"
    />

    @if ($selectedTrigger && $selectedTrigger::getComponent())
        @livewire($selectedTrigger::getComponent(), [
            'triggerClass' => $selectedTrigger,
            'automation' => $automation,
        ])
    @endif

    <div class="form-buttons">
        <button type="submit" class="button">
            <x-mailcoach::icon-label icon="fa-cog" :text="__('Save')" />
        </button>
    </div>
</form>
