<div>
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
            <x-mailcoach::text-field placeholder="john@example.com, jane@example.com" :label="__('mailcoach - To')"
                                     name="template.to" wire:model.lazy="template.to"/>
            <x-mailcoach::text-field placeholder="john@example.com, jane@example.com" :label="__('mailcoach - Cc')"
                                     name="template.cc" wire:model.lazy="template.cc"/>
            <x-mailcoach::text-field placeholder="john@example.com, jane@example.com" :label="__('mailcoach - Bcc')"
                                     name="template.bcc" wire:model.lazy="template.bcc"/>
        </x-mailcoach::fieldset>

        <x-mailcoach::fieldset card :legend="__('mailcoach - Email')">

            <x-mailcoach::text-field
                :label="__('mailcoach - Subject')"
                name="template.subject"
                wire:model.lazy="template.subject"
                required
            />

            @if ($template->type === 'html')
                @livewire(\Livewire\Livewire::getAlias(config('mailcoach.template_editor')), ['model' => $template])
            @else
                <?php
                $editor = config('mailcoach.template_editor', \Spatie\Mailcoach\Domain\Shared\Support\Editor\TextEditor::class);
                $editorName = (new ReflectionClass($editor))->getShortName();
                ?>
                <x-mailcoach::html-field label="{{ [
                    'html' => 'HTML (' . $editorName . ')',
                    'markdown' => 'Markdown',
                    'blade' => 'Blade',
                    'blade-markdown' => 'Blade with Markdown',
                ][$template->type] }}" name="template.body" wire:model.lazy="template.body" />

                <x-mailcoach::editor-buttons :model="$template" :preview-html="$template->body" />
            @endif
        </x-mailcoach::fieldset>
    </form>

    @if($template->canBeTested())
        <x-mailcoach::modal :title="__('mailcoach - Send Test')" name="send-test" :dismissable="true">
            @include('mailcoach::app.transactionalMails.templates.partials.test')
        </x-mailcoach::modal>
    @endif

    <x-mailcoach::replacer-help-texts :model="$template" />
</div>
