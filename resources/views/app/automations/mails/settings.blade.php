<form
    wire:submit.prevent="save"
    @keydown.prevent.window.cmd.s="$wire.call('save')"
    @keydown.prevent.window.ctrl.s="$wire.call('save')"
    method="POST"
    data-dirty-check
>
<x-mailcoach::card>
    <x-mailcoach::text-field :label="__('mailcoach - Name')" name="mail.name" wire:model.lazy="mail.name" required  />

    <x-mailcoach::text-field :label="__('mailcoach - Subject')" name="mail.subject" wire:model.lazy="mail.subject"  />

    <x-mailcoach::fieldset :legend="__('mailcoach - Tracking')">
        <div class="form-field">
            <x-mailcoach::info>
                {!! __('mailcoach - Open & Click tracking are managed by your email provider.') !!}
            </x-mailcoach::info>
        </div>

        <div class="form-field">
            <label class="label">{{ __('mailcoach - UTM Tags') }}</label>
            <div class="checkbox-group">
                <x-mailcoach::checkbox-field :label="__('mailcoach - Automatically add UTM tags')" name="mail.utm_tags" wire:model="mail.utm_tags" />
            </div>
        </div>

        <x-mailcoach::help>
            <p class="text-sm mb-2">{{ __('mailcoach - When checked, the following UTM Tags will automatically get added to any links in your campaign:') }}</p>
            <ul>
                <li><strong>utm_source</strong>: newsletter</li>
                <li><strong>utm_medium</strong>: email</li>
                <li><strong>utm_campaign</strong>: {{ \Illuminate\Support\Str::slug($mail->name) }}</li>
            </ul>
        </x-mailcoach::help>
    </x-mailcoach::fieldset>

    <x-mailcoach::form-buttons>
        <x-mailcoach::button :label="__('mailcoach - Save settings')" />
    </x-mailcoach::form-buttons>
</x-mailcoach::card>
</form>
