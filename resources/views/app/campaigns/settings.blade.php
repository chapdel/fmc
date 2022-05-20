<form
    class="form-grid"
    method="POST"
    data-dirty-check
    wire:submit.prevent="save"
    @keydown.prevent.window.cmd.s="$wire.call('save')"
    @keydown.prevent.window.ctrl.s="$wire.call('save')"
>
    @csrf

    <x-mailcoach::text-field :label="__('mailcoach - Name')" name="name" wire:model.lazy="campaign.name" required :disabled="!$campaign->isEditable()" />

    <x-mailcoach::text-field :label="__('mailcoach - Subject')" name="subject" wire:model.lazy="campaign.subject" :disabled="!$campaign->isEditable()" />

    @if ($campaign->isEditable())
        @include('mailcoach::app.campaigns.partials.emailListFields', ['segmentable' => $campaign, 'wiremodel' => 'campaign'])
    @else
        <div>
            Sent to list "{{ $campaign->emailList->name }}"

            @if($campaign->tagSegment)
                Used segment {{ $campaign->tagSegment->name }}
            @endif
        </div>
    @endif

    <x-mailcoach::fieldset :legend="__('mailcoach - Tracking')">
        <div class="form-field">
            <label class="label">{{ __('mailcoach - Track when…') }}</label>
            <div class="checkbox-group">
                <x-mailcoach::checkbox-field :label="__('mailcoach - Someone opens this email')" name="track_opens" wire:model.lazy="campaign.track_opens" :disabled="!$campaign->isEditable()" />
                <x-mailcoach::checkbox-field :label="__('mailcoach - Links in the email are clicked')" name="track_clicks" wire:model.lazy="campaign.track_clicks" :disabled="!$campaign->isEditable()" />
            </div>
        </div>

        <div class="form-field">
            <label class="label">{{ __('mailcoach - UTM Tags') }}</label>
            <div class="checkbox-group">
                <x-mailcoach::checkbox-field :label="__('mailcoach - Automatically add UTM tags')" name="utm_tags" wire:model="campaign.utm_tags" :disabled="!$campaign->isEditable()" />
            </div>
        </div>

        <x-mailcoach::help>
            <p class="text-sm mb-2">{{ __('mailcoach - When checked, the following UTM Tags will automatically get added to any links in your campaign:') }}</p>
            <ul>
                <li><strong>utm_source</strong>: newsletter</li>
                <li><strong>utm_medium</strong>: email</li>
                <li><strong>utm_campaign</strong>: {{ $campaign->name }}</li>
            </ul>
        </x-mailcoach::help>
    </x-mailcoach::fieldset>

    @if ($campaign->isEditable())
        <div class="form-buttons">
            <x-mailcoach::button :label="__('mailcoach - Save settings')" />
        </div>
    @endif
</form>
