<form class="form-grid" wire:submit.prevent="saveTemplate" method="POST">
    <x-mailcoach::text-field
        :label="__('mailcoach - Name')"
        name="name"
        wire:model.lazy="name"
        :placeholder="__('mailcoach - Transactional mail template')"
        required
    />

    <?php
        $editor = config('mailcoach.template_editor', \Spatie\Mailcoach\Domain\Shared\Support\Editor\TextEditor::class);
        $editorName = (new ReflectionClass($editor))->getShortName();
    ?>
    <x-mailcoach::select-field
        :label="__('mailcoach - Type')"
        name="type"
        wire:model.lazy="type"
        :options="[
            'html' => 'HTML (' . $editorName . ')',
            'markdown' => 'Markdown',
            'blade' => 'Blade',
            'blade-markdown' => 'Blade with Markdown',
        ]"
    />

    <div class="form-buttons">
        <x-mailcoach::button :label="__('mailcoach - Create template')" />
        <x-mailcoach::button-cancel x-on:click="$store.modals.close('create-transactional-template')" />
    </div>
</form>
