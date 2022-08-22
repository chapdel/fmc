<div>
@include('mailcoach::app.configuration.mailers.wizards.wizardNavigation')
<x-mailcoach::card>

    <x-mailcoach::help>
        <p>In order not to overwhelm your provider with send requests, Mailcoach will throttle the amount of mails sent.</p>
        <p>When your account is in sandbox mode, the maximum amount of emails you can send is 1 / second, once your account is out of sandbox mode, you'll find the limit in your SES Account Dashboard</p>
        <p>You can find more info about sending limits in <a href="https://docs.aws.amazon.com/ses/latest/dg/manage-sending-quotas.html" target="_blank">the Amazon SES documentation</a>.</p>
    </x-mailcoach::help>

        <form class="form-grid" wire:submit.prevent="submit">

            <x-mailcoach::text-field
                wire:model.defer="timespanInSeconds"
                :label="__('Timespan in seconds')"
                name="timespanInSeconds"
                type="number"
            />

            <x-mailcoach::text-field
                wire:model.defer="mailsPerTimeSpan"
                :label="__('Mails per timespan')"
                name="mailsPerTimeSpan"
                type="number"
            />

            <x-mailcoach::form-buttons>
                <x-mailcoach::button :label="__('Save')"/>
        </x-mailcoach::form-buttons>
        </form>
    </x-mailcoach::card>
</div>
