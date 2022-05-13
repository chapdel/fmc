<form class="form-grid" wire:submit.prevent="saveTag" method="POST">
    @csrf

    <x-mailcoach::text-field :label="__('mailcoach - Name')" wire:model="name" name="name" required />

    <div class="form-buttons">
        <x-mailcoach::button :label="__('mailcoach - Create tag')"/>
        <button type="button" class="button-cancel" x-on:click="$store.modals.close('create-tag')">
            {{ __('mailcoach - Cancel') }}
        </button>
    </div>
</form>
