<form
    class="form-grid"
    wire:submit.prevent="save(new URLSearchParams(new FormData($event.target)).toString())"
    method="POST"
>
    <x-mailcoach::text-field :label="__('mailcoach - Name')" name="automation.name" wire:model.lazy="automation.name" required />

    <x-mailcoach::select-field
        :label="__('mailcoach - Trigger')"
        name="trigger"
        :options="$triggerOptions"
        placeholder="Select a trigger"
        required
        wire:model="selectedTrigger"
    />

    <div>
        @if ($selectedTrigger && $selectedTrigger::getComponent())
            @livewire($selectedTrigger::getComponent(), [
                'triggerClass' => $automation->triggerClass(),
                'automation' => $automation,
            ], key($selectedTrigger))
        @endif
    </div>

    @include('mailcoach::app.campaigns.partials.emailListFields', ['segmentable' => $automation, 'wiremodel' => 'automation'])

    <div class="form-buttons">
        <x-mailcoach::button :label="__('mailcoach - Save')" />
    </div>
</form>
