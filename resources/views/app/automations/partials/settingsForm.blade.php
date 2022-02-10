<form
    class="form-grid"
    action="{{ route('mailcoach.automations.settings', $automation) }}"
    method="POST"
>
    @csrf
    @method('PUT')

    <x-mailcoach::text-field :label="__('mailcoach - Name')" name="name" :value="$automation->name" required />

    <x-mailcoach::select-field
        :label="__('mailcoach - Trigger')"
        name="trigger"
        :options="$triggerOptions"
        placeholder="Select a trigger"
        required
        value="{{ old('trigger', $automation->triggerClass()) }}"
        wire:change="setSelectedTrigger($event.target.value)"
    />

    @if ($selectedTrigger && $selectedTrigger::getComponent())
        @livewire($selectedTrigger::getComponent(), [
            'triggerClass' => $automation->triggerClass(),
            'automation' => $automation,
        ], key($selectedTrigger))
    @endif

    @include('mailcoach::app.campaigns.partials.emailListFields', ['segmentable' => $automation])

    <div class="form-buttons">
        <x-mailcoach::button :label="__('mailcoach - Save')" />
    </div>
</form>
