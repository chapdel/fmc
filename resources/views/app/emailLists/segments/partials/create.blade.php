<form class="form-grid" wire:submit.prevent="saveSegment" method="POST">
    @csrf
    <x-mailcoach::text-field :label="__('mailcoach - Name')" name="name" wire:model.lazy="name" required />

    <div class="form-buttons">
        <x-mailcoach::button :label="__('mailcoach - Create segment')" />

        <button type="button" class="button-cancel" data-modal-dismiss>
            {{ __('mailcoach - Cancel') }}
        </button>
    </div>
</form>
