<form class="form-grid" wire:submit.prevent="saveSubscriber" method="POST">
    @csrf
    <x-mailcoach::text-field :label="__('mailcoach - Email')" wire:model.lazy="email" name="email" type="email" required />
    <x-mailcoach::text-field :label="__('mailcoach - First name')" wire:model.lazy="first_name" name="first_name" />
    <x-mailcoach::text-field :label="__('mailcoach - Last name')" wire:model.lazy="last_name" name="last_name" />

    <div class="form-buttons">
        <x-mailcoach::button :label="__('mailcoach - Add subscriber')" />
        <button type="button" class="button-cancel" x-on:click="$store.modals.close('create-subscriber')">
            {{ __('mailcoach - Cancel') }}
        </button>
    </div>
</form>
