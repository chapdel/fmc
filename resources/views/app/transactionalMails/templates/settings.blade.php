<x-mailcoach::layout-transactional-template title="Settings" :template="$template">
        <form
            class="form-grid"
            method="POST"
        >
            @csrf
            @method('PUT')

            <x-mailcoach::help>
                <p class="text-sm mb-2">{{ __('Blade templates have the ability to run arbitrary PHP code. Only select Blade if you trust all users that have access to the Mailcoach UI.') }}</p>
            </x-mailcoach::help>


            <x-mailcoach::select-field
                :label="__('Type')"
                name="type"
                :value="$template->type"
                :options="[
                    'html' => 'HTML',
                    'markdown' => 'Markdown',
                    'blade' => 'Blade',
                ]"
            />

            <x-mailcoach::checkbox-field :label="__('Store mail')" name="store_mail" :checked="$template->store_mail" />

            <x-mailcoach::fieldset :legend="__('Tracking')">
                <div class="form-field">
                    <label class="label">{{ __('Track when…') }}</label>
                    <div class="checkbox-group">
                        <x-mailcoach::checkbox-field :label="__('Someone opens this email')" name="track_opens" :checked="$template->track_opens" />
                        <x-mailcoach::checkbox-field :label="__('Links in the email are clicked')" name="track_clicks" :checked="$template->track_clicks" />
                    </div>
                </div>
            </x-mailcoach::fieldset>

            <div class="form-buttons">
                <x-mailcoach::button :label="__('Save settings')" />
            </div>
        </form>
    </section>
</x-mailcoach::layout-transactional-template>

