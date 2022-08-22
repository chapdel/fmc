<form
    method="POST"
    wire:submit.prevent="save"
    @keydown.prevent.window.cmd.s="$wire.call('save')"
    @keydown.prevent.window.ctrl.s="$wire.call('save')"
>
<x-mailcoach::card>
    <x-mailcoach::text-field :label="__('mailcoach - Name')" name="tag.name" wire:model="tag.name" required />

    <x-mailcoach::help>
        <p>{{ __('Whether the subscriber can choose to add or remove this tag on "Manage your preferences" page.') }}</p>
        <p>{!! __('This page can be linked to by using the <code>::preferencesUrl::</code> placeholder in your emails.') !!}</p>
        <p>{!! __('You can view an example of your page with all the currently enabled tags <a href=":url">here</a>', [
            'url' => route('mailcoach.manage-preferences', ['example', $tag->emailList->uuid]),
        ]) !!}</p>
    </x-mailcoach::help>
    <x-mailcoach::checkbox-field :label="__('mailcoach - Visible on manage preferences page')" name="tag.visible_in_preferences" wire:model="tag.visible_in_preferences" required />

    <x-mailcoach::form-buttons>
        <x-mailcoach::button :label="__('mailcoach - Save tag')" />
    </x-mailcoach::form-buttons>
</x-mailcoach::card>
</form>
