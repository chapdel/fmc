<form
    method="POST"
    wire:submit.prevent="save"
    @keydown.prevent.window.cmd.s="$wire.call('save')"
    @keydown.prevent.window.ctrl.s="$wire.call('save')"
>
    <x-mailcoach::card>
        <x-mailcoach::help>
            <p>
                Mailcoach can create a website that displays the content of each mail you send to this list. This way,
                people not subscribed on your list can still read your content.
            </p>
            <p>
                @if($emailList->has_website)
                    Your website is available at <a
                        href="{{ $emailList->websiteUrl() }}">{{ $emailList->websiteUrl() }}</a>
                @endif
            </p>
        </x-mailcoach::help>

        <x-mailcoach::checkbox-field
            :label="__('mailcoach - Enable website')"
            name="emailList.has_website"
            wire:model.lazy="emailList.has_website"
        />

        <x-mailcoach::text-field
            :label="__('mailcoach - Slug')"
            name="emailList.website_slug"
            wire:model.lazy="emailList.website_slug"
        />

        <x-mailcoach::info>
            {{ __('You can choose a url where you website will be displayed.') }}
        </x-mailcoach::info>


        <x-mailcoach::form-buttons>
            <x-mailcoach::button :label="__('mailcoach - Save')"/>
        </x-mailcoach::form-buttons>
    </x-mailcoach::card>
</form>

