<form
    class="form-grid"
    action="{{ route('mailcoach.automations.actions.store', $automation) }}"
    method="POST"
>
    @csrf
    @method('POST')
    <livewire:automation-builder :automation="$automation" :componentData="['actions' => $actions]" />

    <div>
        <button type="submit" class="button" {{ $editing ? 'disabled="disabled"' : '' }}>
            <x-mailcoach::icon-label icon="fa-save" :text="__('Save')" />
        </button>
    </div>
</form>
