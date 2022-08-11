<div class="card-grid">
    @include('mailcoach::app.configuration.mailers.wizards.wizardNavigation')

    <x-mailcoach::success>
        <p>
            Your SMTP mailer has been set up. We highly recommend sending a small test campaign to yourself to check if
            everything is working as expected.
        </p>
    </x-mailcoach::success>

    <x-mailcoach::fieldset card :legend="__('Summary')">
        <dl class="dl">
            <dt>Host</dt>
            <dd>
                {{ $mailer->get('host') }}
            </dd>

            <dt>Port</dt>
            <dd>
                {{ $mailer->get('port') }}
            </dd>

            <dt>Username</dt>
            <dd>
                {{ $mailer->get('username') }}
            </dd>

            <dt>Encryption</dt>
            <dd>
                {{ $mailer->get('encryption') === '' ? 'None' : $mailer->get('encryption') }}
            </dd>
        </dl>
    </x-mailcoach::fieldset>

    <x-mailcoach::fieldset card :legend="__('Throttling')">
         <dl class="dl">
            <dt>Timespan in seconds</dt>
            <dd>
                {{ $mailer->get('timespan_in_seconds') }}
            </dd>

            <dt>Mails per timespan</dt>
            <dd>
                {{ $mailer->get('mails_per_timespan') }}
            </dd>
        </dl>
    </x-mailcoach::fieldset>

    @include('mailcoach::app.configuration.mailers.partials.mailerName')

    <x-mailcoach::card buttons>
    <x-mailcoach::button class="mt-4" :label="__('Send test email')" x-on:click.prevent="$store.modals.open('send-test')" />
    </x-mailcoach::card>
</div>
