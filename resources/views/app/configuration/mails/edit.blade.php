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
        <x-mailcoach::suppression-list />

        <x-mailcoach::form-buttons>
            <x-mailcoach::button :label="__mc('Save')"/>
        </x-mailcoach::form-buttons>
    </x-mailcoach::card>


</form>
