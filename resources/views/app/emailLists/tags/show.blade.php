<form
    class="form-grid"
    method="POST"
    wire:submit.prevent="save"
    @keydown.prevent.window.cmd.s="$wire.call('save')"
    @keydown.prevent.window.ctrl.s="$wire.call('save')"
>
    <x-mailcoach::text-field :label="__('mailcoach - Name')" name="tag.name" wire:model="tag.name" required />

    <div class="form-buttons">
        <x-mailcoach::button :label="__('mailcoach - Save tag')" />
    </div>
</form>
