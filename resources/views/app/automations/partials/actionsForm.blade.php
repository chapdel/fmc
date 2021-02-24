<form
    class="form-grid"
    action="{{ route('mailcoach.automations.actions.store', $automation) }}"
    method="POST"
>
    @csrf
    @method('POST')
    <livewire:automation-builder :automation="$automation" :actions="$actions" />

    <div class="form-buttons">
        <x-mailcoach::button :label="__('Save actions')" :disabled="count($editingActions) > 0" />
    </div>
</form>
