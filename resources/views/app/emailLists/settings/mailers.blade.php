<form
    method="POST"
    wire:submit.prevent="save"
    @keydown.prevent.window.cmd.s="$wire.call('save')"
    @keydown.prevent.window.ctrl.s="$wire.call('save')"
>
<x-mailcoach::card>

    @if(count(config('mail.mailers')) > 1)
        <x-mailcoach::select-field
            name="campaign_mailer"
            :options="array_combine(array_keys(config('mail.mailers')), array_keys(config('mail.mailers')))"
            wire:model="emailList.campaign_mailer"
            :label="__('mailcoach - Campaign mailer')"
        />
        <x-mailcoach::info>{{ __('mailcoach - The mailer used for sending campaigns.') }}</x-mailcoach::info>

        <x-mailcoach::select-field
            name="automation_mailer"
            :options="array_combine(array_keys(config('mail.mailers')), array_keys(config('mail.mailers')))"
            wire:model="emailList.automation_mailer"
            :label="__('mailcoach - Automation mailer')"
        />
        <x-mailcoach::info>{{ __('mailcoach - The mailer used for sending automations.') }}</x-mailcoach::info>

        <x-mailcoach::select-field
            name="transactional_mailer"
            :options="array_combine(array_keys(config('mail.mailers')), array_keys(config('mail.mailers')))"
            wire:model="emailList.transactional_mailer"
            :label="__('mailcoach - Transactional mailer')"
        />
        <x-mailcoach::info>{{ __('mailcoach - The mailer used for sending transactional mails.') }}</x-mailcoach::info>
    @else
        <x-mailcoach::info>{{ __('mailcoach - No mailers set.') }}</x-mailcoach::info>
    @endif

    <x-mailcoach::form-buttons>
        <x-mailcoach::button :label="__('mailcoach - Save')"/>
    </x-mailcoach::form-buttons>
</x-mailcoach::card>
</form>

