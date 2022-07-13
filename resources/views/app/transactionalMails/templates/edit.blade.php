<form
class="card-grid"
method="POST"
wire:submit.prevent="save"
@keydown.prevent.window.cmd.s="$wire.call('save')"
@keydown.prevent.window.ctrl.s="$wire.call('save')"
>
<x-mailcoach::fieldset card :legend="__('mailcoach - Recipients')">
    <x-mailcoach::info>
        {{ __('mailcoach - These recipients will be merged with the ones when the mail is sent. You can specify multiple recipients comma separated.') }}
    </x-mailcoach::info>
    <x-mailcoach::text-field placeholder="john@example.com, jane@example.com" :label="__('mailcoach - To')" name="template.to" wire:model.lazy="template.to"/>
    <x-mailcoach::text-field placeholder="john@example.com, jane@example.com" :label="__('mailcoach - Cc')" name="template.cc" wire:model.lazy="template.cc"/>
    <x-mailcoach::text-field placeholder="john@example.com, jane@example.com" :label="__('mailcoach - Bcc')" name="template.bcc" wire:model.lazy="template.bcc"/>
</x-mailcoach::fieldset>

<x-mailcoach::fieldset card :legend="__('mailcoach - Email')">
        
<x-mailcoach::text-field :label="__('mailcoach - Subject')" name="template.subject" wire:model.lazy="template.subject" required/>

        @livewire(\Livewire\Livewire::getAlias(config('mailcoach.template_editor')), ['model' => $template])
    </x-mailcoach::fieldset>
    </form>

    <x-mailcoach::preview-modal :title="__('mailcoach - Preview') . ' - ' . $template->subject" :html="$template->body"/>

    @if($template->canBeTested())
        <x-mailcoach::modal :title="__('mailcoach - Send Test')" name="send-test">
            @include('mailcoach::app.transactionalMails.templates.partials.test')
        </x-mailcoach::modal>
    @endif

    <x-mailcoach::transactional-mail-template-replacer-help-texts :template="$template"/>
