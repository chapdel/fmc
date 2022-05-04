<form class="form-grid" wire:submit.prevent="saveAutomation" method="POST">
    @csrf

    <x-mailcoach::text-field
        :label="__('mailcoach - Name')"
        name="name"
        wire:model.lazy="name"
        :placeholder="__('mailcoach - Automation name')"
        required
    />

    <div class="form-buttons">
        <x-mailcoach::button :label="__('mailcoach - Create automation')"/>
        <x-mailcoach::button-cancel :label="__('mailcoach - Cancel')" x-on:click="$store.modals.close('create-automation')" />
    </div>
</form>
