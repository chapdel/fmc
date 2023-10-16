@php
    $linkDescriptions = [];

    if ($this->campaign->emailList?->websiteEnabled()) {
        $linkDescriptions[] = '<a target=_blank href="' . $this->campaign->emailList->websiteUrl() . '">the public website</a>';
    }

    if ($this->campaign->emailList?->campaigns_feed_enabled) {
        $linkDescriptions[] = 'the RSS feed';
    }

    $linkDescriptions = collect($linkDescriptions)->join(', ', ' and ');
@endphp

<form
    class="card-grid"
    method="POST"
    data-dirty-check
    wire:submit="save"
    @keydown.prevent.window.cmd.s="$wire.call('save')"
    @keydown.prevent.window.ctrl.s="$wire.call('save')"
>
    @csrf

    <x-mailcoach::card>
        <x-mailcoach::text-field :label="__mc('Name')" name="form.name" wire:model="form.name" required :disabled="!$campaign->isEditable()" />
    </x-mailcoach::card>

    @if ($campaign->isEditable())
        @include('mailcoach::app.campaigns.partials.emailListFields', ['segmentable' => $campaign, 'wiremodel' => 'form'])
    @else
        <x-mailcoach::fieldset card legend="Audience">
            <div>
                @if($campaign->emailList)
                    Sent to list <a href="{{ route('mailcoach.emailLists.subscribers', $campaign->emailList) }}"><strong>{{ $campaign->emailList->name }}</strong></a>
                @else
                    Sent to list <strong>{{ __mc('deleted list') }}</strong>
                @endif

            @if($campaign->tagSegment)
                , used segment <strong>{{ $campaign->tagSegment->name }}</strong>
            @endif
            </div>
        </x-mailcoach::fieldset>
    @endif

    <x-mailcoach::fieldset card :legend="__mc('Sender')">
        @if ($campaign->isEditable())
        <x-mailcoach::info class="-mt-4">{!! __mc('Leave empty to use your <a href=":url">email list defaults</a>', ['url' => $campaign->emailList ? route('mailcoach.emailLists.general-settings', $campaign->emailList) : '']) !!}</x-mailcoach::info>
        @endif
        <div class="grid grid-cols-2 gap-6">
            <x-mailcoach::text-field :label="__mc('From email')" name="form.from_email" wire:model="form.from_email"
                                     type="email" :placeholder="$campaign->emailList?->default_from_email" :disabled="!$campaign->isEditable()" />

            <x-mailcoach::text-field :label="__mc('From name')" name="form.from_name" wire:model="form.from_name" :placeholder="$campaign->emailList?->default_from_name" :disabled="!$campaign->isEditable()"/>

            <x-mailcoach::text-field
                :label="__mc('Reply-to email')"
                name="form.reply_to_email"
                wire:model="form.reply_to_email"
                :help="__mc('Use a comma separated list to send replies to multiple email addresses.')"
                :placeholder="$campaign->emailList?->default_reply_to_email"
                :disabled="!$campaign->isEditable()"
            />

            <x-mailcoach::text-field
                :label="__mc('Reply-to name')"
                name="form.reply_to_name"
                wire:model="form.reply_to_name"
                :placeholder="$campaign->emailList?->default_reply_to_name"
                :help="__mc('Use a comma separated list to send replies to multiple email addresses.')"
                :disabled="!$campaign->isEditable()"
            />
        </div>
    </x-mailcoach::fieldset>

    <x-mailcoach::fieldset card :legend="__mc('Tracking')">
        <div class="form-field">
            @php([$openTracking, $clickTracking] = $campaign->tracking())
            @if (!is_null($openTracking) || !is_null($clickTracking))
                @php($mailerModel = $campaign->getMailer())
                <x-mailcoach::help>
                    {!! __mc('Open & Click tracking are managed by your email provider, this campaign uses the <a href=":mailerLink"><strong>:mailer</strong></a> mailer.', ['mailer' => $mailerModel->name, 'mailerLink' => route('mailers.edit', $mailerModel)]) !!}

                    <div class="mt-4">
                        <x-mailcoach::health-label warning :test="$openTracking" :label="$openTracking ? __mc('Open tracking enabled') : __mc('Open tracking disabled')" />
                    </div>
                    <div class="mt-2">
                        <x-mailcoach::health-label warning :test="$clickTracking" :label="$clickTracking ? __mc('Click tracking enabled') : __mc('Click tracking disabled')" />
                    </div>
                </x-mailcoach::help>


            @elseif($campaign->emailList?->campaign_mailer)
                <x-mailcoach::info>
                    {!! __mc('Open & Click tracking are managed by your email provider, this campaign uses the <strong>:mailer</strong> mailer.', ['mailer' => $campaign->emailList->campaign_mailer]) !!}
                </x-mailcoach::info>
            @else
                <x-mailcoach::info>
                    {!! __mc('Your email list does not have a mailer set up yet.') !!}
                </x-mailcoach::info>
            @endif
        </div>

        <div class="form-field">
            <label class="label">{{ __mc('Subscriber Tags') }}</label>
            <div class="checkbox-group">
                <x-mailcoach::checkbox-field :label="__mc('Add tags to subscribers for opens & clicks')" name="form.add_subscriber_tags" wire:model.live="form.add_subscriber_tags" :disabled="!$campaign->isEditable()" />
                <x-mailcoach::checkbox-field :label="__mc('Add individual link tags')" name="form.add_subscriber_link_tags" wire:model.live="form.add_subscriber_link_tags" :disabled="!$campaign->isEditable()" />
            </div>
        </div>

        @if ($form->add_subscriber_tags || $form->add_subscriber_link_tags)
            <x-mailcoach::help max-width="2xl">
                @if ($form->add_subscriber_tags)
                    <p class="text-sm mb-2">{{ __mc('The following tags will automatically get added to subscribers that open or click the campaign:') }}</p>
                    <p x-data="{}">
                        <x-mailcoach::code-copy class="flex gap-x-2 mb-1" code='{{ "campaign-{$campaign->uuid}-opened" }}' />
                        <x-mailcoach::code-copy class="flex gap-x-2" code='{{ "campaign-{$campaign->uuid}-clicked" }}' />
                    </p>
                @endif
                @if ($form->add_subscriber_link_tags)
                    <p class="text-sm">{{ __mc('Subscribers will receive a unique tag per link clicked.') }}</p>
                @endif
            </x-mailcoach::help>
        @endif

        <div class="form-field">
            <label class="label">
                {{ __mc('UTM Tags') }}

                <i class="ml-1 text-purple-500 opacity-75 cursor-pointer fas fa-question-circle" x-data x-tooltip="@js(__mc('Automatically add UTM tags to any links'))"></i>
            </label>
            <div class="checkbox-group">
                <x-mailcoach::checkbox-field :label="__mc('Automatically add UTM tags')" name="form.utm_tags" wire:model.live="form.utm_tags" :disabled="!$campaign->isEditable()" />
            </div>
        </div>

        @if ($form->utm_tags)
            <x-mailcoach::help>
                <p class="text-sm mb-2">{{ __mc('The following UTM Tags will automatically get added to any links in your campaign:') }}</p>
                <dl class="markup-dl">
                    <dt><strong>utm_source</strong></dt><dd>{{ $form->utm_source }}</dd>
                    <dt><strong>utm_medium</strong></dt><dd>{{ $form->utm_medium }}</dd>
                    <dt><strong>utm_campaign</strong></dt><dd>{{ $form->utm_campaign }}</dd>
                </dl>
            </x-mailcoach::help>
        @endif
    </x-mailcoach::fieldset>

    <x-mailcoach::fieldset card :legend="__mc('Publish campaign')">
        @if($this->campaign->emailList?->has_website || $this->campaign->emailList?->campaigns_feed_enabled)
            <div>
                <x-mailcoach::help>
                    When this campaign has been sent, we can display the content on {!! $linkDescriptions !!} for this email list.
                </x-mailcoach::help>
            </div>

            <div class="form-field">
                <div class="checkbox-group">
                    <x-mailcoach::checkbox-field :label="__mc('Show publicly')" name="form.show_publicly" wire:model="form.show_publicly" />
                </div>
            </div>
        @endif

            <x-mailcoach::info>
                {!! __mc('Webview allows users to view the content of the email in a browser.') !!}
            </x-mailcoach::info>

        <div class="form-field">
            <div class="checkbox-group">
                <x-mailcoach::checkbox-field :label="__mc('Disable Webview')" name="form.disable_webview" wire:model="form.disable_webview" />
            </div>
        </div>
    </x-mailcoach::fieldset>

    <x-mailcoach::fieldset card :legend="__mc('Usage in Mailcoach API')">
        <div>
            <x-mailcoach::help>
                {!! __mc('Whenever you need to specify a <code>:resourceName</code> in the Mailcoach API and want to use this :resource, you\'ll need to pass this value', [
                'resourceName' => 'campaign uuid',
                'resource' => 'campaign',
            ]) !!}
                <p class="mt-4">
                    <x-mailcoach::code-copy class="flex items-center justify-between max-w-md" :code="$campaign->uuid"></x-mailcoach::code-copy>
                </p>
            </x-mailcoach::help>
        </div>
    </x-mailcoach::fieldset>

    <x-mailcoach::card buttons>
        <x-mailcoach::button :label="__mc('Save settings')" />
    </x-mailcoach::card>
</form>
