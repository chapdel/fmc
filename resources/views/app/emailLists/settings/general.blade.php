<form
    class="card-grid"
    method="POST"
    wire:submit="save"
    @keydown.prevent.window.cmd.s="$wire.call('save')"
    @keydown.prevent.window.ctrl.s="$wire.call('save')"
>
    <x-mailcoach::fieldset card :legend="__mc('Settings')">
        <x-mailcoach::text-field :label="__mc('Name')" name="form.name" wire:model="form.name" required/>

        <div class="form-field max-w-full">
            <label class="label" for="form.campaigns_feed_enabled">{{ __mc('Publication') }}</label>
            <x-mailcoach::checkbox-field
                :label="__mc('Make RSS feed publicly available')"
                name="form.campaigns_feed_enabled"
                wire:model.live="form.campaigns_feed_enabled"
            />
            @if ($this->form->campaigns_feed_enabled)
                <x-mailcoach::info class="mt-2" full>
                    {{ __mc('Your public feed will be available at') }}
                    <a class="text-sm link" target="_blank" href="{{$emailList->feedUrl()}}">{{$emailList->feedUrl()}}</a>
                </x-mailcoach::info>
            @endif
        </div>
    </x-mailcoach::fieldset>

    <x-mailcoach::fieldset card :legend="__mc('Sender')">

        <div class="grid grid-cols-2 gap-6">
            <x-mailcoach::text-field :label="__mc('From email')" name="form.default_from_email" wire:model.lazy="form.default_from_email"
                        type="email" required/>

            <x-mailcoach::text-field :label="__mc('From name')" name="form.default_from_name" wire:model.lazy="form.default_from_name"/>

            <x-mailcoach::text-field
                :label="__mc('Reply-to email')"
                name="form.default_reply_to_email"
                :help="__mc('Use a comma separated list to send replies to multiple email addresses.')"
                wire:model.lazy="form.default_reply_to_email"
            />

            <x-mailcoach::text-field
                :label="__mc('Reply-to name')"
                name="form.default_reply_to_name"
                :help="__mc('Use a comma separated list to send replies to multiple email addresses.')"
                wire:model.lazy="form.default_reply_to_name"
            />
        </div>
    </x-mailcoach::fieldset>

    <x-mailcoach::fieldset card :legend="__mc('Email Notifications')">
        <div class="checkbox-group">
            <x-mailcoach::checkbox-field :label="__mc('Confirmation when a campaign has finished sending to this list')"
                            name="form.report_campaign_sent" wire:model.live="form.report_campaign_sent"/>
            <x-mailcoach::checkbox-field
                :label="__mc('Summary of opens, clicks & bounces a day after a campaign has been sent to this list')"
                name="form.report_campaign_summary" wire:model.live="form.report_campaign_summary"/>
            <x-mailcoach::checkbox-field :label="__mc('Weekly summary on the subscriber growth of this list')"
                            name="form.report_email_list_summary" wire:model.live="form.report_email_list_summary"/>
        </div>

        @if ($this->form->report_campaign_sent || $this->form->report_campaign_summary || $this->form->report_email_list_summary)
            <x-mailcoach::text-field
                :help="__mc('Which email address(es) should the notifications be sent to?')"
                :placeholder="__mc('Email(s) comma separated')"
                :label="__mc('Email')"
                name="form.report_recipients"
                wire:model.lazy="form.report_recipients"
            />
        @endif
    </x-mailcoach::fieldset>

    <x-mailcoach::fieldset card :legend="__mc('Usage in Mailcoach API')">
        <div>
            <x-mailcoach::help>
                {!! __mc('Whenever you need to specify a <code>:resourceName</code> in the Mailcoach API and want to use this :resource, you\'ll need to pass this value', [
                'resourceName' => 'emailList uuid',
                'resource' => 'email list',
            ]) !!}
                <p class="mt-4">
                    <x-mailcoach::code-copy class="flex items-center justify-between max-w-md" :code="$emailList->uuid"></x-mailcoach::code-copy>
                </p>
            </x-mailcoach::help>
        </div>
    </x-mailcoach::fieldset>

    <x-mailcoach::card buttons>
        <x-mailcoach::button :label="__mc('Save')"/>
    </x-mailcoach::card>
</form>

