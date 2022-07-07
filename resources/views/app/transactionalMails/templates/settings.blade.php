<form
    class="form-grid"
    method="POST"
    wire:submit.prevent="save"
    @keydown.prevent.window.cmd.s="$wire.call('save')"
    @keydown.prevent.window.ctrl.s="$wire.call('save')"
    x-data="{ type: @entangle('template.type') }"
    x-cloak
>
    <x-mailcoach::fieldset :legend="__('mailcoach - General')">
        <x-mailcoach::text-field :label="__('mailcoach - Name')" name="template.name" wire:model.lazy="template.name" required />
        <x-mailcoach::info>
            {{ __('mailcoach - This name is used by the application to retrieve this template. Do not change it without updating the code of your app.') }}
        </x-mailcoach::info>

        <?php
        $editor = config('mailcoach.template_editor', \Spatie\Mailcoach\Domain\Shared\Support\Editor\TextEditor::class);
        $editorName = (new ReflectionClass($editor))->getShortName();
        ?>
        <x-mailcoach::select-field
            :label="__('mailcoach - Format')"
            name="template.type"
            x-model="type"
            :options="[
                'html' => 'HTML (' . $editorName . ')',
                'markdown' => 'Markdown',
                'blade' => 'Blade',
                'blade-markdown' => 'Blade with Markdown',
            ]"
        />

        <div x-show="type === 'blade'">
            <x-mailcoach::warning>
                <p class="text-sm mb-2">{{ __('mailcoach - Blade templates have the ability to run arbitrary PHP code. Only select Blade if you trust all users that have access to the Mailcoach UI.') }}</p>
            </x-mailcoach::warning>
        </div>

        <div x-show="type === 'blade-markdown'">
            <x-mailcoach::warning>
                <p class="text-sm mb-2">{{ __('mailcoach - Blade templates have the ability to run arbitrary PHP code. Only select Blade if you trust all users that have access to the Mailcoach UI.') }}</p>
            </x-mailcoach::warning>
        </div>

        <x-mailcoach::checkbox-field :label="__('mailcoach - Store mail')" name="template.store_mail" wire:model.lazy="template.store_mail" />
    </x-mailcoach::fieldset>

    <x-mailcoach::fieldset :legend="__('mailcoach - Tracking')">
        <div class="form-field">
            <label class="label">{{ __('mailcoach - Track when…') }}</label>
            <div class="checkbox-group">
                <x-mailcoach::checkbox-field :label="__('mailcoach - Someone opens this email')" name="template.track_opens" wire:model.lazy="template.track_opens" />
                <x-mailcoach::checkbox-field :label="__('mailcoach - Links in the email are clicked')" name="template.track_clicks" wire:model.lazy="template.track_clicks" />
            </div>
        </div>
    </x-mailcoach::fieldset>

    <div class="form-buttons">
        <x-mailcoach::button :label="__('mailcoach - Save settings')" />
    </div>
</form>
