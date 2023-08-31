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
        <h2 class="text-lg font-bold">Suppression List</h2>
        <x-mailcoach::info full>
            {{ __mc('Your suppression list contains all the recipients who can no longer receive emails. Recipients who unsubscribe, hard bounce, or mark your emails as spam are automatically added to this list.') }}
        </x-mailcoach::info>
        <livewire:mailcoach::suppression-list  />
    </x-mailcoach::card>


</form>
