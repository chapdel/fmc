<div>
    <x-html-field :label="__('Body (HTML)')" name="html" :value="old('html', $html)"></x-html-field>
</div>

<div class="form-buttons">
    <button id="save" type="submit" class="button">
        <x-icon-label icon="fa-code" :text="__('Save content')"/>
    </button>

    <button type="button" class="link-icon" data-modal-trigger="preview">
        <x-icon-label icon="fa-eye" :text="__('Preview')"/>
    </button>
    <x-modal :title="__('Preview')" name="preview" large>
        <iframe class="absolute" width="100%" height="100%" data-html-preview-target></iframe>
    </x-modal>
</div>

<x-replacer-help-texts />
