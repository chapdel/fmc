<form
    class="form-grid"
    method="POST"
    wire:submit.prevent="save"
    @keydown.prevent.window.cmd.s="$wire.call('save')"
    @keydown.prevent.window.ctrl.s="$wire.call('save')"
>
    @if(count(config('mail.mailers')) > 1)
        <x-mailcoach::fieldset :legend="__('mailcoach - Mailers')">

        <x-mailcoach::select-field
            name="campaign_mailer"
            :options="array_combine(array_keys(config('mail.mailers')), array_keys(config('mail.mailers')))"
            wire:model="emailList.campaign_mailer"
            :label="__('mailcoach - Campaign mailer')"
        />
        <x-mailcoach::help>{{ __('mailcoach - The mailer used for sending campaigns.') }}</x-mailcoach::help>

        <x-mailcoach::select-field
            name="automation_mailer"
            :options="array_combine(array_keys(config('mail.mailers')), array_keys(config('mail.mailers')))"
            wire:model="emailList.automation_mailer"
            :label="__('mailcoach - Automation mailer')"
        />
        <x-mailcoach::help>{{ __('mailcoach - The mailer used for sending automations.') }}</x-mailcoach::help>


        <x-mailcoach::select-field
            name="transactional_mailer"
            :options="array_combine(array_keys(config('mail.mailers')), array_keys(config('mail.mailers')))"
            wire:model="emailList.transactional_mailer"
            :label="__('mailcoach - Transactional mailer')"
        />
        <x-mailcoach::help>{{ __('mailcoach - The mailer used for sending transactional mails.') }}</x-mailcoach::help>

        </x-mailcoach::fieldset>
    @endif

    <div class="form-buttons">
        <x-mailcoach::button :label="__('mailcoach - Save')"/>
    </div>
</form>

