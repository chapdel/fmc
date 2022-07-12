<form
    wire:submit.prevent="save"
    @keydown.prevent.window.cmd.s="$wire.call('save')"
    @keydown.prevent.window.ctrl.s="$wire.call('save')"
    method="POST"
>
<x-mailcoach::card class="form-fieldsets-no-max-w">    
    <livewire:mailcoach::automation-builder name="default" :automation="$automation" :actions="$actions" />

    <div class="mb-48">
        @if ($unsavedChanges)
            <x-mailcoach::warning
                        @lang('You have unsaved changes.')
            </x-mailcoach::warning
        @endif
        <x-mailcoach::form-buttons>
            <x-mailcoach::button :label="__('mailcoach - Save actions')" :disabled="count($editingActions) > 0" />
        </x-mailcoach::form-buttons>
    </div>
</x-mailcoach::card>    
</form>
