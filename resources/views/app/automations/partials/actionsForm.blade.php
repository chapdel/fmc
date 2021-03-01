<form
    class="form-grid"
    action="{{ route('mailcoach.automations.actions.store', $automation) }}"
    method="POST"
>
    @csrf
    @method('POST')
    <livewire:automation-builder name="default" :automation="$automation" :actions="$actions" />

    <div class="mb-48 form-buttons">
        <x-mailcoach::button :label="__('Save actions')" :disabled="count($editingActions) > 0" />
    </div>
</form>
