<form
    class="form-grid"
    wire:submit.prevent="saveSegment"
    @keydown.prevent.window.cmd.s="$wire.call('saveSegment')"
    @keydown.prevent.window.ctrl.s="$wire.call('saveSegment')"
    method="POST"
>
    @csrf
    <x-mailcoach::text-field :label="__('mailcoach - Name')" name="name" wire:model.lazy="name" required />

    <div class="form-buttons">
        <x-mailcoach::button :label="__('mailcoach - Create segment')" />

        <button type="button" class="button-cancel" x-on:click="$store.modals.close('create-segment')">
            {{ __('mailcoach - Cancel') }}
        </button>
    </div>
</form>
