<form
    class="card-grid"
    wire:submit.prevent="save"
    @keydown.prevent.window.cmd.s="$wire.call('save')"
    @keydown.prevent.window.ctrl.s="$wire.call('save')"
    method="POST"
    data-dirty-check
>
<x-mailcoach::card>
    <x-mailcoach::text-field :label="__('mailcoach - Name')" name="mail.name" wire:model.lazy="mail.name" required  />

    <x-mailcoach::text-field :label="__('mailcoach - Subject')" name="mail.subject" wire:model.lazy="mail.subject"  />
</x-mailcoach::card>

    <x-mailcoach::fieldset card :legend="__('mailcoach - Tracking')">
        <div class="form-field">
            <x-mailcoach::info>
                {!! __('mailcoach - Open & Click tracking are managed by your email provider.') !!}
            </x-mailcoach::info>
        </div>

        <div class="form-field">
            <label class="label">{{ __('mailcoach - Subscriber Tags') }}</label>
            <div class="checkbox-group">
                <x-mailcoach::checkbox-field :label="__('mailcoach - Add tags to subscribers for opens & clicks')" name="mail.add_subscriber_tags" wire:model="mail.add_subscriber_tags" />
                <x-mailcoach::checkbox-field :label="__('mailcoach - Add individual link tags')" name="mail.add_subscriber_link_tags" wire:model="mail.add_subscriber_link_tags" />
            </div>
        </div>

        <x-mailcoach::help>
            <p class="text-sm mb-2">{{ __('mailcoach - When checked, the following tags will automatically get added to subscribers that open or click the automation mail:') }}</p>
            <p>
                <span class="tag-neutral">{{ "automation-mail-{$mail->uuid}-opened" }}</span>
                <span class="tag-neutral">{{ "automation-mail-{$mail->uuid}-clicked" }}</span>
            </p>
            <p class="text-sm mt-2">{{ __('mailcoach - When "Add individual link tags" is checked, it will also add a unique tag per link') }}</p>
        </x-mailcoach::help>

        <div class="form-field">
            <label class="label">{{ __('mailcoach - UTM Tags') }}</label>
            <div class="checkbox-group">
                <x-mailcoach::checkbox-field :label="__('mailcoach - Automatically add UTM tags')" name="mail.utm_tags" wire:model="mail.utm_tags" />
            </div>
        </div>

        <x-mailcoach::help>
            <p class="text-sm mb-2">{{ __('mailcoach - When checked, the following UTM Tags will automatically get added to any links in your campaign:') }}</p>
            <dl class="markup-dl">
                <dt><strong>utm_source</strong></dt><dd>newsletter</dd>
                <dt><strong>utm_medium</strong></dt><dd>email</dd>
                <dt><strong>utm_campaign</strong></dt><dd>{{ \Illuminate\Support\Str::slug($mail->name) }}</dd>
            </dl>
        </x-mailcoach::help>
    </x-mailcoach::fieldset>

    <x-mailcoach::card  buttons>
        <x-mailcoach::button :label="__('mailcoach - Save settings')" />
    </x-mailcoach::card>
</form>
