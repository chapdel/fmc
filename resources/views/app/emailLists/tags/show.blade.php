<form
    method="POST"
    wire:submit="save"
    @keydown.prevent.window.cmd.s="$wire.call('save')"
    @keydown.prevent.window.ctrl.s="$wire.call('save')"
>
<x-mailcoach::card>
    <x-mailcoach::text-field :label="__mc('Name')" name="name" wire:model="name" required />

    <x-mailcoach::help>
        <p>{{ __mc('Whether the subscriber can choose to add or remove this tag on "Manage your preferences" page.') }}</p>
        <p>{!! __mc('This page can be linked to by using the <code>::preferencesUrl::</code> placeholder in your emails.') !!}</p>
        <p>{!! __mc('You can view an example of your page with all the currently enabled tags <a href=":url">here</a>', [
            'url' => route('mailcoach.manage-preferences', ['example', $tag->emailList->uuid]),
        ]) !!}</p>
    </x-mailcoach::help>
    <x-mailcoach::checkbox-field :label="__mc('Visible on manage preferences page')" name="visible_in_preferences" wire:model="visible_in_preferences" />

    <x-mailcoach::form-buttons>
        <x-mailcoach::button :label="__mc('Save tag')" />
    </x-mailcoach::form-buttons>
</x-mailcoach::card>
</form>
