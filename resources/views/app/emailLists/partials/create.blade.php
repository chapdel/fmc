<form
    class="form-grid"
    wire:submit.prevent="saveList"
    @keydown.prevent.window.cmd.s="$wire.call('saveList')"
    @keydown.prevent.window.ctrl.s="$wire.call('saveList')"
    method="POST"
>
    <x-mailcoach::text-field :label="__('mailcoach - Name')"  wire:model.lazy="name" name="name" :placeholder="__('mailcoach - Subscribers')" required />
    <x-mailcoach::text-field :label="__('mailcoach - From email')" :placeholder="auth()->user()->email" wire:model.lazy="default_from_email" name="default_from_email" type="email" required />
    <x-mailcoach::text-field :label="__('mailcoach - From name')" :placeholder="auth()->user()->name" wire:model.lazy="default_from_name" name="default_from_name" />

    <div class="form-buttons">
        <x-mailcoach::button :label="__('mailcoach - Create list')" />
        <button type="button" class="button-cancel"  x-on:click="$store.modals.close('create-list')">
            {{ __('mailcoach - Cancel') }}
        </button>
    </div>
</form>
