<div>
    <x-mailcoach::warning>
        <p>{{ __mc('Unlayer editor stores content in a structured way. When switching from or to Unlayer, content in existing draft campaigns might get lost.') }}</p>
    </x-mailcoach::warning>

    <x-mailcoach::info class="mt-6">
        <p>{!! __mc('<a href=":link">Unlayer</a> is a beautiful editor that allows you to edit html in a structured way. You don\'t need any HTML knowledge to compose a campaign. This editor also allows image uploads.', ['link' => 'https://unlayer.com']) !!}</p>
        <p>{{ __mc('Using this editor disables template functionality within Mailcoach as it has its own templating functionality') }}</p>
    </x-mailcoach::info>

    <x-mailcoach::fieldset>
        <div>
            <x-mailcoach::text-field
                :label="__mc('Unlayer Project ID')"
                name="editorSettings.project_id"
                wire:model.lazy="editorSettings.project_id"
                type="text"
            />
            <x-mailcoach::info class="mt-1">
                {{ __mc('If you have a paid Unlayer account, you can enter your project ID here') }}
            </x-mailcoach::info>
        </div>
    </x-mailcoach::fieldset>



</div>
