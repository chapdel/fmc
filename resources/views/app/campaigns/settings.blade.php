@php
    $linkDescriptions = [];

    if ($this->campaign->emailList->has_website) {
        $linkDescriptions[] = '<a target=_blank href="' . $this->campaign->emailList->websiteUrl() . '">the public website</a>';
    }

    if ($this->campaign->emailList->campaigns_feed_enabled) {
        $linkDescriptions[] = 'the RSS feed';
    }

    $linkDescriptions = collect($linkDescriptions)->join(', ', ' and ');
@endphp

<form
    class="card-grid"
    method="POST"
    data-dirty-check
    wire:submit.prevent="save"
    @keydown.prevent.window.cmd.s="$wire.call('save')"
    @keydown.prevent.window.ctrl.s="$wire.call('save')"
>
    @csrf

    <x-mailcoach::card>
        <x-mailcoach::text-field :label="__('mailcoach - Name')" name="name" wire:model.lazy="campaign.name" required :disabled="!$campaign->isEditable()" />

        <x-mailcoach::text-field :label="__('mailcoach - Subject')" name="subject" wire:model.lazy="campaign.subject" :disabled="!$campaign->isEditable()" />
    </x-mailcoach::card>

    @if ($campaign->isEditable())
        @include('mailcoach::app.campaigns.partials.emailListFields', ['segmentable' => $campaign, 'wiremodel' => 'campaign'])
    @else
        <x-mailcoach::fieldset card legend="Audience">
            <div>
            Sent to list <strong>{{ $campaign->emailList?->name ?? __('mailcoach - deleted list') }}</strong>

            @if($campaign->tagSegment)
                , used segment <strong>{{ $campaign->tagSegment->name }}</strong>
            @endif
            </div>
        </x-mailcoach::fieldset>
    @endif


    <x-mailcoach::fieldset card :legend="__('mailcoach - Tracking')">
        <div class="form-field">
            @php([$openTracking, $clickTracking] = $campaign->tracking())
            @if (!is_null($openTracking) || !is_null($clickTracking))
                @php($mailerModel = $campaign->getMailer())
                <x-mailcoach::help>
                    {!! __('mailcoach - Open & Click tracking are managed by your email provider, this campaign uses the <a href=":mailerLink"><strong>:mailer</strong></a> mailer.', ['mailer' => $mailerModel->name, 'mailerLink' => route('mailers.edit', $mailerModel)]) !!}

                    <div class="mt-4">
                        <x-mailcoach::health-label warning :test="$openTracking" :label="$openTracking ? __('mailcoach - Open tracking enabled') : __('mailcoach - Open tracking disabled')" />
                    </div>
                    <div class="mt-2">
                        <x-mailcoach::health-label warning :test="$clickTracking" :label="$clickTracking ? __('mailcoach - Click tracking enabled') : __('mailcoach - Click tracking disabled')" />
                    </div>
                </x-mailcoach::help>


            @elseif($campaign->emailList?->campaign_mailer)
                <x-mailcoach::info>
                    {!! __('mailcoach - Open & Click tracking are managed by your email provider, this campaign uses the <strong>:mailer</strong> mailer.', ['mailer' => $campaign->emailList->campaign_mailer]) !!}
                </x-mailcoach::info>
            @else
                <x-mailcoach::info>
                    {!! __('mailcoach - Your email list does not have a mailer set up yet.') !!}
                </x-mailcoach::info>
            @endif
        </div>

        <div class="form-field">
            <label class="label">{{ __('mailcoach - Subscriber Tags') }}</label>
            <div class="checkbox-group">
                <x-mailcoach::checkbox-field :label="__('mailcoach - Add tags to subscribers for opens & clicks')" name="campaign.add_subscriber_tags" wire:model="campaign.add_subscriber_tags" :disabled="!$campaign->isEditable()" />
                <x-mailcoach::checkbox-field :label="__('mailcoach - Add individual link tags')" name="campaign.add_subscriber_link_tags" wire:model="campaign.add_subscriber_link_tags" :disabled="!$campaign->isEditable()" />
            </div>
        </div>

        <x-mailcoach::help>
            <p class="text-sm mb-2">{{ __('mailcoach - When checked, the following tags will automatically get added to subscribers that open or click the campaign:') }}</p>
                <p>
                    <span class="tag-neutral">{{ "campaign-{$campaign->uuid}-opened" }}</span>
                    <span class="tag-neutral">{{ "campaign-{$campaign->uuid}-clicked" }}</span>
                </p>
            <p class="text-sm mt-2">{{ __('mailcoach - When "Add individual link tags" is checked, it will also add a unique tag per link') }}</p>
        </x-mailcoach::help>

        <div class="form-field">
            <label class="label">{{ __('mailcoach - UTM Tags') }}</label>
            <div class="checkbox-group">
                <x-mailcoach::checkbox-field :label="__('mailcoach - Automatically add UTM tags')" name="utm_tags" wire:model="campaign.utm_tags" :disabled="!$campaign->isEditable()" />
            </div>
        </div>

        <x-mailcoach::help>
            <p class="text-sm mb-2">{{ __('mailcoach - When checked, the following UTM Tags will automatically get added to any links in your campaign:') }}</p>
            <dl class="markup-dl">
                <dt><strong>utm_source</strong></dt><dd>newsletter</dd>
                <dt><strong>utm_medium</strong></dt><dd>email</dd>
                <dt><strong>utm_campaign</strong></dt><dd>{{ \Illuminate\Support\Str::slug($campaign->name) }}</dd>
            </dl>
        </x-mailcoach::help>
    </x-mailcoach::fieldset>



    @if($this->campaign->emailList->has_website || $this->campaign->emailList->campaigns_feed_enabled)
        <x-mailcoach::fieldset card :legend="__('Publish campaign')">
            <div>
                <x-mailcoach::help>
                    When this campaign has been sent, we can display the content on {!! $linkDescriptions !!} for this email list.
                </x-mailcoach::help>
            </div>

            <div class="form-field">
                <div class="checkbox-group">
                    <x-mailcoach::checkbox-field :label="__('mailcoach - Show publicly')" name="utm_tags" wire:model="campaign.show_publicly" />
                </div>
            </div>

        </x-mailcoach::fieldset>
    @endif

    <x-mailcoach::fieldset card :legend="__('Usage in Mailcoach API')">
        <div>
            <x-mailcoach::help>
                {!! __('mailcoach - Whenever you need to specify a <code>:resourceName</code> in the Mailcoach API and want to use this :resource, you\'ll need to pass this value', [
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
            <x-mailcoach::button :label="__('mailcoach - Save settings')" />
        </x-mailcoach::card>

</form>
