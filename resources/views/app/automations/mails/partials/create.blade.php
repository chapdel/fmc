<form class="form-grid w-64"
      wire:submit.prevent="saveAutomationMail"
      @keydown.prevent.window.cmd.s="$wire.call('saveAutomationMail')"
      @keydown.prevent.window.ctrl.s="$wire.call('saveAutomationMail')"
      method="POST"
>
    @csrf

    <x-mailcoach::text-field
        :label="__mc('Name')"
        name="name"
        wire:model.lazy="name"
        :placeholder="__mc('Email name')"
        required
    />

    @if(count($templateOptions) > 1)
        <x-mailcoach::select-field
            :label="__mc('Template')"
            :options="$templateOptions"
            wire:model.lazy="template_id"
            name="template_id"
        />
    @endif

    <x-mailcoach::form-buttons>
        <x-mailcoach::button :label="__mc('Create email')" />
        <x-mailcoach::button-cancel  x-on:click="$store.modals.close('create-automation-mail')" />
    </x-mailcoach::form-buttons>
</form>
