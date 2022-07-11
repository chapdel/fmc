<div>
    <form
        class="form-grid"
        wire:submit.prevent="save"
        @keydown.prevent.window.cmd.s="$wire.call('save')"
        @keydown.prevent.window.ctrl.s="$wire.call('save')"
        method="POST"
    >
        <x-mailcoach::text-field :label="__('mailcoach - Name')" name="template.name" wire:model="template.name" required />

        @livewire(\Livewire\Livewire::getAlias(config('mailcoach.template_editor')), [
            'model' => $template,
        ])
    </form>
</div>
