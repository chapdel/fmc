<div>
    <livewire:mailcoach::mailgun-configuration :mailer="$mailer" />
    <x-mailcoach::modal name="send-test">
        <livewire:mailcoach::mailer-send-test mailer="{{ $mailer->configName() }}" />
    </x-mailcoach::modal>
</div>
