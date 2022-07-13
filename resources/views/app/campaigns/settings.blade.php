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
        <x-mailcoach::card>
            Sent to list "{{ $campaign->emailList->name }}"

            @if($campaign->tagSegment)
                Used segment {{ $campaign->tagSegment->name }}
            @endif
        </x-mailcoach::card>
    @endif
   

    <x-mailcoach::fieldset card :legend="__('mailcoach - Tracking')">
        <div class="form-field">
            @php($mailerClass = config('mailcoach-ui.models.mailer'))
            @if (class_exists($mailerClass) && $mailerModel = $mailerClass::all()->first(fn ($mailerModel) => $mailerModel->configName() === $mailer))
                @if ($mailerModel->get('open_tracking_enabled', false))
                    <x-mailcoach::icon-label icon="fas fa-check text-green-500" :text="__('mailcoach - Open tracking enabled')" />
                @else
                    <x-mailcoach::icon-label icon="fas fa-times text-red-500" :text="__('mailcoach - Open tracking disabled')" />
                @endif
                @if ($mailerModel->get('click_tracking_enabled', false))
                    <x-mailcoach::icon-label icon="fas fa-check text-green-500" :text="__('mailcoach - Click tracking enabled')" />
                @else
                    <x-mailcoach::icon-label icon="fas fa-times text-red-500" :text="__('mailcoach - Click tracking disabled')" />
                @endif
                <x-mailcoach::info>
                    {!! __('mailcoach - Open & Click tracking are managed by your email provider, this campaign uses the <a href=":mailerLink"><strong>:mailer</strong></a> mailer.', ['mailer' => $mailerModel->name, 'mailerLink' => route('mailers.edit', $mailerModel)]) !!}
                </x-mailcoach::info>
            @elseif($mailer)
                <x-mailcoach::info>
                    {!! __('mailcoach - Open & Click tracking are managed by your email provider, this campaign uses the <strong>:mailer</strong> mailer.', ['mailer' => $mailer]) !!}
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
