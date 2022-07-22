<form
    method="POST"
    wire:submit.prevent="save"
    @keydown.prevent.window.cmd.s="$wire.call('save')"
    @keydown.prevent.window.ctrl.s="$wire.call('save')"
>
<x-mailcoach::card>
    <x-mailcoach::help>
        {{ __('mailcoach - Select a mailer for each of the functionalities of Mailcoach. If you leave them empty, the default mailer or the mailer set in your configuration file will be used.') }}
    </x-mailcoach::help>

    @if(count(config('mail.mailers')) > 1)
        <x-mailcoach::select-field
            name="campaign_mailer"
            :options="array_combine(array_keys(config('mail.mailers')), array_keys(config('mail.mailers')))"
            :placeholder="__('Select a mailer')"
            :clearable="true"
            wire:model="emailList.campaign_mailer"
            :label="__('mailcoach - Campaign mailer')"
        />

        <x-mailcoach::select-field
            name="automation_mailer"
            :options="array_combine(array_keys(config('mail.mailers')), array_keys(config('mail.mailers')))"
            :placeholder="__('Select a mailer')"
            :clearable="true"
            wire:model="emailList.automation_mailer"
            :label="__('mailcoach - Automation mailer')"
        />

        <x-mailcoach::select-field
            name="transactional_mailer"
            :options="array_combine(array_keys(config('mail.mailers')), array_keys(config('mail.mailers')))"
            :placeholder="__('Select a mailer')"
            :clearable="true"
            wire:model="emailList.transactional_mailer"
            :label="__('mailcoach - Transactional mailer')"
        />
    @else
        <x-mailcoach::info>{{ __('mailcoach - No mailers set.') }}</x-mailcoach::info>
    @endif

    <x-mailcoach::form-buttons>
        <x-mailcoach::button :label="__('mailcoach - Save')"/>
    </x-mailcoach::form-buttons>
</x-mailcoach::card>
</form>

