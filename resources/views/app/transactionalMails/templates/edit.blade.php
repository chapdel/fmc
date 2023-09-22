<div>
    <form
            class="card-grid"
            method="POST"
            wire:submit="save"
            @keydown.prevent.window.cmd.s="$wire.call('save')"
            @keydown.prevent.window.ctrl.s="$wire.call('save')"
    >
        <x-mailcoach::fieldset card :legend="__mc('Recipients')">
            <x-mailcoach::info>
                {{ __mc('These recipients will be merged with the ones when the mail is sent. You can specify multiple recipients comma separated.') }}
            </x-mailcoach::info>
            <x-mailcoach::text-field placeholder="john@example.com, jane@example.com" :label="__mc('To')"
                                     name="to" wire:model.lazy="to"/>
            <x-mailcoach::text-field placeholder="john@example.com, jane@example.com" :label="__mc('Cc')"
                                     name="cc" wire:model.lazy="cc"/>
            <x-mailcoach::text-field placeholder="john@example.com, jane@example.com" :label="__mc('Bcc')"
                                     name="bcc" wire:model.lazy="bcc"/>
        </x-mailcoach::fieldset>

        <x-mailcoach::fieldset card :legend="__mc('Email')">

            <x-mailcoach::text-field
                    :label="__mc('Subject')"
                    name="subject"
                    wire:model.lazy="subject"
                    required
            />

            @if ($template->type === 'html')
                <div class="mt-6">
                    @livewire(\Livewire\Livewire::getAlias(config('mailcoach.content_editor')), ['model' => $template->contentItem])
                </div>
            @else
                    <?php
                    $editor = config('mailcoach.content_editor',
                        \Spatie\Mailcoach\Livewire\Editor\TextAreaEditorComponent::class);
                    $editorName = (new ReflectionClass($editor))->getShortName();
                    ?>
                <x-mailcoach::html-field label="{{ [
                    'html' => 'HTML (' . $editorName . ')',
                    'markdown' => 'Markdown',
                    'blade' => 'Blade',
                    'blade-markdown' => 'Blade with Markdown',
                ][$template->type] }}" name="html" wire:model.lazy="html"/>

                <x-mailcoach::editor-buttons :model="$template" :preview-html="$template->body"/>
            @endif
        </x-mailcoach::fieldset>
    </form>

    <x-mailcoach::replacer-help-texts :model="$template"/>
</div>
