<div>
    <x-mailcoach::html-field :label="__('mailcoach - Body (HTML)')" name="html" :value="old('html', $html)"></x-mailcoach::html-field>
</div>

<x-mailcoach::form-buttons>
    <x-mailcoach::button id="save" :label="__('mailcoach - Save content of mail')"/>
    <x-mailcoach::button-secondary x-on:click="$store.modals.open('preview')" :label="__('mailcoach - Preview')"/>
    <x-mailcoach::button-secondary x-on:click="$store.modals.open('send-test')" :label="__('mailcoach - Send Test')"/>
</x-mailcoach::form-buttons>


