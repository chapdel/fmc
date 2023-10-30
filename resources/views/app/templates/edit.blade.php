<div>
    <x-mailcoach::card>
        <x-mailcoach::help full>
            <p>{{ __mc('A template is a reusable layout that can be used as a starting point for your campaigns, automation emails or transactional mails.') }}</p>
            <p>{!! __mc('Create slots in your template by adding the name in triple brackets, for example: <code>[[[content]]]</code>. You can add as many slots as you like.') !!}</p>
            <span>{!! __mc('By default your chosen editor will be loaded, you can append <code>:text</code> to your placeholder for a simple text input, or <code>:image</code> for an image upload that will fill the uploaded URL in the slot, for example: <code>[[[preheader:text]]]</code> or <code>[[[logo:image]]]</code>') !!}</span>
        </x-mailcoach::help>

        <form
            class="form-grid mt-6"
            wire:submit="save"
            @keydown.prevent.window.cmd.s="$wire.call('save')"
            @keydown.prevent.window.ctrl.s="$wire.call('save')"
            method="POST"
        >
            <x-mailcoach::text-field :label="__mc('Name')" name="name" wire:model="name" required />

            @livewire(config('mailcoach.template_editor'), [
                'model' => $template,
                'quiet' => true,
            ])

            <x-mailcoach::form-buttons>
                <x-mailcoach::replacer-help-texts :model="$template" />

                <x-mailcoach::form-buttons>
                    <div class="flex gap-x-2">
                        <x-mailcoach::button
                            wire:click.prevent="save"
                            :label="__mc('Save template')"
                        />

                        @if (config('mailcoach.template_editor') !== \Spatie\Mailcoach\Domain\Editor\Unlayer\Editor::class)
                            <x-mailcoach::button-secondary
                                x-on:click.prevent="$dispatch('open-modal', { id: 'preview-{{ md5($html) }}' })"
                                :label="__mc('Preview')"
                            />
                            <template x-teleport="body">
                                <x-mailcoach::preview-modal
                                    id="preview-{{ md5($html) }}"
                                    :html="$html"
                                    :title="__mc('Preview')"
                                />
                            </template>
                        @endif
                    </div>

                    @if (! preg_match_all('/\[\[\[(.*?)\]\]\]/', $html, $matches))
                        <x-mailcoach::info class="mt-6">
                            {!! __mc('We found no slots in this template. You can add slots by adding the name in triple brackets, for example: <code>[[[content]]]</code>.') !!}
                        </x-mailcoach::info>
                    @endif
                </x-mailcoach::form-buttons>

            </x-mailcoach::form-buttons>
        </form>
    </x-mailcoach::card>

    <x-mailcoach::fieldset class="mt-6" card :legend="__mc('Usage in Mailcoach API')">
        <div>
            <x-mailcoach::help>
                {!! __mc('Whenever you need to specify a <code>:resourceName</code> in the Mailcoach API and want to use this :resource, you\'ll need to pass this value', [
                'resourceName' => 'template uuid',
                'resource' => 'template',
            ]) !!}
                <p class="mt-4">
                    <x-mailcoach::code-copy class="flex items-center justify-between max-w-md" :code="$template->uuid"></x-mailcoach::code-copy>
                </p>
            </x-mailcoach::help>
        </div>
    </x-mailcoach::fieldset>
</div>
