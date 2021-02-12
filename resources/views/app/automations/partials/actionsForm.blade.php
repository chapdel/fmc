<form
    class="form-grid"
    action="{{ route('mailcoach.automations.actions.store', $automation) }}"
    method="POST"
>
    @csrf
    @method('POST')
    <livewire:automation-builder :automation="$automation" :componentData="['actions' => $actions]" />

    <div class="form-buttons">
        <x-mailcoach::button :label="__('Save actions')" :disabled="$editing" />
    </div>
</form>
