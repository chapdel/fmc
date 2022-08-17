<x-mailcoach::card>
    <x-mailcoach::help>
        <p>{{ __('mailcoach - A template is a reusable layout that can be used as a starting point for your campaigns, automation emails or transactional mails.') }}</p>
        <p>{!! __('mailcoach - Create slots in your template by adding the name in triple brackets, for example: <code>[[[content]]]</code>. You can add as many slots as you like.') !!}</p>
        <span>{!! __('mailcoach - You can add a normal text field by appending <code>:text</code> to your placeholder, for example: <code>[[[preheader:text]]]</code>') !!}</span>
    </x-mailcoach::help>

    <form
        class="form-grid mt-6"
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
</x-mailcoach::card>
