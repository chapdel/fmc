<form
    class="form-grid"
    method="POST"
    wire:submit.prevent="save"
    @keydown.prevent.window.cmd.s="$wire.call('save')"
    @keydown.prevent.window.ctrl.s="$wire.call('save')"
>
    <x-mailcoach::fieldset :legend="__('mailcoach - Sender')">
        <x-mailcoach::text-field :label="__('mailcoach - Name')" name="emailList.name" wire:model="emailList.name" required/>

        <x-mailcoach::text-field :label="__('mailcoach - From email')" name="emailList.default_from_email" wire:model.lazy="emailList.default_from_email"
                    type="email" required/>

        <x-mailcoach::text-field :label="__('mailcoach - From name')" name="emailList.default_from_name" wire:model.lazy="emailList.default_from_name"/>

        <x-mailcoach::text-field :label="__('mailcoach - Reply-to email')" name="emailList.default_reply_to_email" wire:model.lazy="emailList.default_reply_to_email"
                    type="email"/>

        <x-mailcoach::text-field :label="__('mailcoach - Reply-to name')" name="emailList.default_reply_to_name" wire:model.lazy="emailList.default_reply_to_name"/>
    </x-mailcoach::fieldset>

    <x-mailcoach::fieldset :legend="__('mailcoach - Publication')">
        <div class="form-field max-w-full">
            <x-mailcoach::checkbox-field :label="__('mailcoach - Make feed publicly available')" name="emailList.campaigns_feed_enabled"
                            wire:model="emailList.campaigns_feed_enabled"/>
            <a class="text-sm link" target="_blank" href="{{$emailList->feedUrl()}}">{{$emailList->feedUrl()}}</a>
        </div>
    </x-mailcoach::fieldset>

    <x-mailcoach::fieldset :legend="__('mailcoach - Notifications')">
        <div class="checkbox-group">
            <x-mailcoach::checkbox-field :label="__('mailcoach - Confirmation when campaign has been sent to this list')"
                            name="emailList.report_campaign_sent" wire:model="emailList.report_campaign_sent"/>
            <x-mailcoach::checkbox-field
                :label="__('mailcoach - Summary of opens, clicks & bounces a day after a campaign has been sent to this list')"
                name="emailList.report_campaign_summary" wire:model="emailList.report_campaign_summary"/>
            <x-mailcoach::checkbox-field :label="__('mailcoach - Weekly summary on the subscriber growth of this list')"
                            name="emailList.report_email_list_summary" wire:model="emailList.report_email_list_summary"/>
        </div>

        @if ($emailList->report_campaign_sent || $emailList->report_campaign_summary || $emailList->report_email_list_summary)
            <x-mailcoach::help>
                {{ __('mailcoach - Which email address(es) should the notifications be sent to?') }}
            </x-mailcoach::help>
            <x-mailcoach::text-field :placeholder="__('mailcoach - Email(s) comma separated')" :label="__('mailcoach - Email')" name="emailList.report_recipients"
                        wire:model.lazy="emailList.report_recipients"/>
        @endif
    </x-mailcoach::fieldset>

    <div class="form-buttons">
        <x-mailcoach::button :label="__('mailcoach - Save')"/>
    </div>
</form>

