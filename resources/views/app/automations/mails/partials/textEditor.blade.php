<div>
    <x-mailcoach::html-field :label="__('mailcoach - Body (HTML)')" name="html" :value="old('html', $html)"></x-mailcoach::html-field>
</div>

<div class="form-buttons">
    <x-mailcoach::button id="save" :label="__('mailcoach - Save content of mail')"/>
    <x-mailcoach::button-secondary data-modal-trigger="preview" :label="__('mailcoach - Preview')"/>
    <x-mailcoach::button-secondary data-modal-trigger="send-test" :label="__('mailcoach - Send Test')"/>
</div>


