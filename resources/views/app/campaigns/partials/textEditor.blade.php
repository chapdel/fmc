<div>
    <x-mailcoach::html-field :label="__('Body (HTML)')" name="html" :value="old('html', $html)"></x-mailcoach::html-field>
</div>

<div class="form-buttons">
    <x-mailcoach::button id="save" :label="__('Save content')"/>
    <x-mailcoach::button-secondary data-modal-trigger="preview" :label="__('Preview')"/>
    <x-mailcoach::button-secondary data-modal-trigger="send-test" :label="__('Send Test')"/>

</div>

<x-mailcoach::modal :title="__('Preview') . ' - ' . $campaign->subject" name="preview" large :open="Request::get('modal')">
    <iframe class="absolute" width="100%" height="100%" data-html-preview-target></iframe>
</x-mailcoach::modal>

<x-mailcoach::modal :title="__('Send Test')" name="send-test">
    @include('mailcoach::app.campaigns.partials.test')
</x-mailcoach::modal>
