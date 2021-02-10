<div>
    <x-mailcoach::html-field :label="__('Body')" name="html"
                             :value="old('html', $html)"></x-mailcoach::html-field>
</div>

<div class="form-buttons">
    <x-mailcoach::button id="save" :label="__('Save content')"/>

    @if($template->canBeTested())
        <x-mailcoach::button-secondary data-modal-trigger="send-test" :label="__('Send Test')"/>
    @endif

</div>

<x-mailcoach::modal :title="__('Send Test')" name="send-test">
    @include('mailcoach::app.transactionalMails.templates.partials.test')
</x-mailcoach::modal>
