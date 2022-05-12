<form
    class="form-grid"
    wire:submit.prevent="save"
    method="POST"
>
    <livewire:mailcoach::automation-builder name="default" :automation="$automation" :actions="$actions" />

    <div class="mb-48">
        @if ($unsavedChanges)
            <div class="alert alert-warning shadow-lg">
                <div class="max-w-layout mx-auto grid gap-1">
                    <div class="flex items-baseline">
                        <span class="w-6"><i class="fas fa-save opacity-50"></i></span>
                        <span class="ml-2 text-sm">
                        @lang('You have unsaved changes.')
                    </span>
                    </div>
                </div>
            </div>
        @endif
        <div class="form-buttons">
            <x-mailcoach::button :label="__('mailcoach - Save actions')" :disabled="count($editingActions) > 0" />
        </div>
    </div>
</form>
