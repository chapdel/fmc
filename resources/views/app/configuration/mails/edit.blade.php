<form
    class="card-grid"
    wire:submit="save"
    @keydown.prevent.window.cmd.s="$wire.call('save')"
    @keydown.prevent.window.ctrl.s="$wire.call('save')"
    method="POST"
    data-cloak
>
    @method('PUT')
    @csrf

    <x-mailcoach::card>
        <livewire:mailcoach::suppression-list  />
    </x-mailcoach::card>


</form>
