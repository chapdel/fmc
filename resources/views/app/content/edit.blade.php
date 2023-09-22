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

        <x-mailcoach::text-field :label="__mc('Subject')" name="subject" wire:model="subject" />
    </form>

    @livewire(config('mailcoach.content_editor'), [
        'model' => $contentItem,
    ])
</x-mailcoach::card>
