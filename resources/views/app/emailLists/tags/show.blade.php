<form
    class="form-grid"
    method="POST"
    wire:submit.prevent="save"
    @keydown.prevent.window.cmd.s="$wire.call('save')"
    @keydown.prevent.window.ctrl.s="$wire.call('save')"
>
    <x-mailcoach::text-field :label="__('mailcoach - Name')" name="tag.name" wire:model="tag.name" required />

    <x-mailcoach::checkbox-field :label="__('mailcoach - Visible on manage preferences page')" name="tag.visible_in_preferences" wire:model="tag.visible_in_preferences" required />

    <div class="form-buttons">
        <x-mailcoach::button :label="__('mailcoach - Save tag')" />
    </div>
</form>
