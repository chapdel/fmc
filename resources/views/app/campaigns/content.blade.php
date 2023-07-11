<x-mailcoach::card>
    <form
        class="card-grid"
        method="POST"
        data-dirty-check
        wire:submit="save"
        @keydown.prevent.window.cmd.s="$wire.call('save')"
        @keydown.prevent.window.ctrl.s="$wire.call('save')"
    >
        @csrf

        <x-mailcoach::text-field :label="__mc('Subject')" name="subject" wire:model.lazy="campaign.subject" :disabled="!$campaign->isEditable()" />
    </form>

    @livewire(Livewire::getAlias(config('mailcoach.content_editor')), [
        'model' => $campaign,
    ])
</x-mailcoach::card>
