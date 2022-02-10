<x-mailcoach::layout-list :title="__('mailcoach - Settings')" :emailList="$emailList">
    <form class="form-grid" method="POST">
        @csrf
        @method('PUT')

        <x-mailcoach::fieldset :legend="__('mailcoach - Sender')">
            <x-mailcoach::text-field :label="__('mailcoach - Name')" name="name" :value="$emailList->name" required/>

            <x-mailcoach::text-field :label="__('mailcoach - From email')" name="default_from_email" :value="$emailList->default_from_email"
                        type="email" required/>

            <x-mailcoach::text-field :label="__('mailcoach - From name')" name="default_from_name" :value="$emailList->default_from_name"/>

            <x-mailcoach::text-field :label="__('mailcoach - Reply-to email')" name="default_reply_to_email" :value="$emailList->default_reply_to_email"
                        type="email"/>

            <x-mailcoach::text-field :label="__('mailcoach - Reply-to name')" name="default_reply_to_name" :value="$emailList->default_reply_to_name"/>
        </x-mailcoach::fieldset>

        <x-mailcoach::fieldset :legend="__('mailcoach - Publication')">
            <div class="form-field max-w-full">
                <x-mailcoach::checkbox-field :label="__('mailcoach - Make feed publicly available')" name="campaigns_feed_enabled"
                                :checked="$emailList->campaigns_feed_enabled"/>
                <a class="text-sm link" href="{{$emailList->feedUrl()}}">{{$emailList->feedUrl()}}</a>
            </div>
        </x-mailcoach::fieldset>

        <x-mailcoach::fieldset :legend="__('mailcoach - Notifications')">
            <div class="checkbox-group">
                <x-mailcoach::checkbox-field :label="__('mailcoach - Confirmation when campaign has been sent to this list')"
                                name="report_campaign_sent" :checked="$emailList->report_campaign_sent"/>
                <x-mailcoach::checkbox-field
                    :label="__('mailcoach - Summary of opens, clicks & bounces a day after a campaign has been sent to this list')"
                    name="report_campaign_summary" :checked="$emailList->report_campaign_summary"/>
                <x-mailcoach::checkbox-field :label="__('mailcoach - Weekly summary on the subscriber growth of this list')"
                                name="report_email_list_summary" :checked="$emailList->report_email_list_summary"/>
            </div>

            <x-mailcoach::text-field :placeholder="__('mailcoach - Email(s) comma separated')" :label="__('mailcoach - Email')" name="report_recipients"
                        :value="$emailList->report_recipients"/>
        </x-mailcoach::fieldset>

        <div class="form-buttons">
            <x-mailcoach::button :label="__('mailcoach - Save')"/>
        </div>
    </form>
</x-mailcoach::layout-list>

