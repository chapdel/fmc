<div>
    <x-mailcoach::html-field :label="__('mailcoach - Body')" name="html"
                             :value="old('html', $html)"></x-mailcoach::html-field>
</div>

<div class="form-buttons">
    <x-mailcoach::button id="save" :label="__('mailcoach - Save content')"/>
    <x-mailcoach::button-secondary x-on:click="$store.modals.open('preview')" :label="__('mailcoach - Preview')"/>

    @if($template->canBeTested())
        <x-mailcoach::button-secondary x-on:click="$store.modals.open('send-test')" :label="__('mailcoach - Send Test')"/>
    @endif

</div>
