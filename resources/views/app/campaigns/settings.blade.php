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

    @if ($campaign->isEditable())
        <x-mailcoach::card buttons>
            <x-mailcoach::button :label="__('mailcoach - Save settings')" />
        </x-mailcoach::card>
    @endif
</form>
