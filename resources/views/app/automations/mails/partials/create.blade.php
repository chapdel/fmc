<form class="form-grid"
      wire:submit.prevent="saveAutomationMail"
      @keydown.prevent.window.cmd.s="$wire.call('saveAutomationMail')"
      @keydown.prevent.window.ctrl.s="$wire.call('saveAutomationMail')"
      method="POST"
>
    @csrf

    <x-mailcoach::text-field
        :label="__('mailcoach - Name')"
        name="name"
        wire:model.lazy="name"
        :placeholder="__('mailcoach - Email name')"
        required
    />

    <div class="form-buttons">
        <x-mailcoach::button :label="__('mailcoach - Create email')" />
        <x-mailcoach::button-cancel  x-on:click="$store.modals.close('create-automation-mail')" />
    </div>
</form>
