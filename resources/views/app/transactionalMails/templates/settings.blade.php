<form
    class="card-grid"
    method="POST"
    wire:submit.prevent="save"
    @keydown.prevent.window.cmd.s="$wire.call('save')"
    @keydown.prevent.window.ctrl.s="$wire.call('save')"
    x-data="{ type: @entangle('template.type') }"
    x-cloak
>
    <x-mailcoach::fieldset card :legend="__('mailcoach - General')">
        <x-mailcoach::text-field :label="__('mailcoach - Name')" name="template.name" wire:model.lazy="template.name" required />
        <x-mailcoach::info>
            {{ __('mailcoach - This name is used by the application to retrieve this template. Do not change it without updating the code of your app.') }}
        </x-mailcoach::info>

        <?php
        $editor = config('mailcoach.template_editor', \Spatie\Mailcoach\Http\App\Livewire\TextAreaEditorComponent::class));
        $editorName = (new ReflectionClass($editor))->getShortName();
        ?>
        <x-mailcoach::select-field
            :label="__('mailcoach - Format')"
            name="template.type"
            wire:model="template.type"
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

    <x-mailcoach::fieldset card :legend="__('mailcoach - Tracking')">
        <div class="form-field">
            <x-mailcoach::info>
                {!! __('mailcoach - Open & Click tracking are managed by your email provider.') !!}
            </x-mailcoach::info>
        </div>
    </x-mailcoach::fieldset>

    <x-mailcoach::card buttons>
        <x-mailcoach::button :label="__('mailcoach - Save settings')" />
</x-mailcoach::card>

</form>
