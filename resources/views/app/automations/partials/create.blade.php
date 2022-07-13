<form
    class="form-grid"
    wire:submit.prevent="saveAutomation"
    @keydown.prevent.window.cmd.s="$wire.call('saveAutomation')"
    @keydown.prevent.window.ctrl.s="$wire.call('saveAutomation')"
    method="POST"
>
    @csrf

    <x-mailcoach::text-field
        :label="__('mailcoach - Name')"
        name="name"
        wire:model.lazy="name"
        :placeholder="__('mailcoach - Automation name')"
        required
    />

    <x-mailcoach::form-buttons>
        <x-mailcoach::button :label="__('mailcoach - Create automation')"/>
        <x-mailcoach::button-cancel :label="__('mailcoach - Cancel')" x-on:click="$store.modals.close('create-automation')" />
    </x-mailcoach::form-buttons>
</form>
