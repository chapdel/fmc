<x-mailcoach::layout-transactional-template title="Settings" :template="$template">
        <form
            class="form-grid"
            method="POST"
        >
            @csrf
            @method('PUT')

            <x-mailcoach::fieldset x-data="{ type: '{{ $template->type }}' }" :legend="__('mailcoach - General')">
                <x-mailcoach::text-field :label="__('mailcoach - Name')" name="name" :value="$template->name" required />
                <x-mailcoach::help>
                    {{ __('mailcoach - This name is used by the application to retrieve this template. Do not change it without updating the code of your app.') }}
                </x-mailcoach::help>


                <?php
                    $editor = config('mailcoach.transactional.editor', \Spatie\Mailcoach\Domain\Shared\Support\Editor\TextEditor::class);
                    $editorName = (new ReflectionClass($editor))->getShortName();
                ?>
                <x-mailcoach::select-field
                    :label="__('mailcoach - Format')"
                    name="type"
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

                <x-mailcoach::checkbox-field :label="__('mailcoach - Store mail')" name="store_mail" :checked="$template->store_mail" />
            </x-mailcoach::fieldset>

            <x-mailcoach::fieldset :legend="__('mailcoach - Tracking')">
                <div class="form-field">
                    <label class="label">{{ __('mailcoach - Track when…') }}</label>
                    <div class="checkbox-group">
                        <x-mailcoach::checkbox-field :label="__('mailcoach - Someone opens this email')" name="track_opens" :checked="$template->track_opens" />
                        <x-mailcoach::checkbox-field :label="__('mailcoach - Links in the email are clicked')" name="track_clicks" :checked="$template->track_clicks" />
                    </div>
                </div>
            </x-mailcoach::fieldset>

            <div class="form-buttons">
                <x-mailcoach::button :label="__('mailcoach - Save settings')" />
            </div>
        </form>
    </section>
</x-mailcoach::layout-transactional-template>

