<form
    class="form-grid"
    method="POST"
    wire:submit.prevent="save"
>
    <x-mailcoach::text-field :label="__('mailcoach - Name')" name="tag.name" wire:model="tag.name" required />

    <div class="form-buttons">
        <x-mailcoach::button :label="__('mailcoach - Save tag')" />
    </div>
</form>
