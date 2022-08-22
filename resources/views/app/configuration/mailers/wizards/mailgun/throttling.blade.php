<div>
    @include('mailcoach::app.configuration.mailers.wizards.wizardNavigation')
    <x-mailcoach::card>
        <x-mailcoach::help>
            <p>In order not to overwhelm your provider with send requests, Mailcoach will throttle the amount of mails sent.</p>
            <p>When your account is in probation, the maximum amount of emails you can send is 100 / hour.</p>
            <p>You can find more info about sending limits in <a href="https://help.mailgun.com/hc/en-us/articles/115001124753-Account-Sending-Limitation" target="_blank">the Mailgun documentation</a>.</p>
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
