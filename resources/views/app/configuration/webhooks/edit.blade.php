<form
    class="card-grid"
    wire:submit.prevent="save"
    @keydown.prevent.window.cmd.s="$wire.call('save')"
    @keydown.prevent.window.ctrl.s="$wire.call('save')"
    method="POST"
>
    @csrf
    @method('PUT')

    <x-mailcoach::card>
        <x-mailcoach::text-field :label="__('Name')" name="webhook.name" wire:model.lazy="webhook.name" required />

        <x-mailcoach::text-field :label="__('URL')" name="webhook.url" wire:model.lazy="webhook.url" required />

        <x-mailcoach::text-field :label="__('Signature header name')" name="webhook.signature_header_name" wire:model.lazy="webhook.signature_header_name" required />

        <x-mailcoach::text-field :label="__('Secret ')" name="webhook.secret" wire:model.lazy="webhook.secret" required />

        <x-mailcoach::checkbox-field :label="__('Use for all lists')" name="webhook.use_for_all_lists" wire:model="webhook.use_for_all_lists" />

        <x-mailcoach::form-buttons>
            <x-mailcoach::button :label="__('Save webhook')" />
        </x-mailcoach::form-buttons>
</x-mailcoach::card>
</form>
