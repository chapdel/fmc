<div>
    <x-mailcoach::html-field :label="__('mailcoach - Body (HTML)')" name="html" :value="old('html', $html)"></x-mailcoach::html-field>
</div>

<div class="form-buttons">
    <x-mailcoach::button id="save" :label="__('mailcoach - Save content')"/>
    <x-mailcoach::button-secondary data-modal-trigger="preview" :label="__('mailcoach - Preview')"/>
</div>

<x-mailcoach::modal :title="__('mailcoach - Preview') . ' - ' . $template->name" name="preview" large :open="Request::get('modal')">
    <iframe class="absolute" width="100%" height="100%" data-html-preview-target></iframe>
</x-mailcoach::modal>
