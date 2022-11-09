<form class="form-grid" wire:submit.prevent="saveWebhook" method="POST">
    @csrf

    <x-mailcoach::text-field type="name" :label="__('Name')" wire:model.lazy="name" name="name" required />
    <x-mailcoach::text-field type="url" :label="__('Url')" wire:model.lazy="url" name="url" required />

    <x-mailcoach::form-buttons>
        <x-mailcoach::button :label="__('Create new webhook')" />

        <button type="button" class="button-cancel" x-on:click="$store.modals.close('create-webhook')">
            {{ __('Cancel') }}
        </button>
    </x-mailcoach::form-buttons>
</form>
