<form class="form-grid" wire:submit.prevent="saveTemplate" method="POST">
    <x-mailcoach::text-field
        :label="__('mailcoach - Name')"
        name="name"
        :placeholder="__('mailcoach - Newsletter template')"
        wire:model.lazy="name"
        required
    />

    <div class="form-buttons">
        <x-mailcoach::button :label="__('mailcoach - Create template')" />
        <x-mailcoach::button-cancel  x-on:click="$store.modals.close('create-template')" />
    </div>
</form>
