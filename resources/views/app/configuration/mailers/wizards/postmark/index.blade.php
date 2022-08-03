<div>
    <livewire:mailcoach::postmark-configuration :mailer="$mailer" />
    <x-mailcoach::modal name="send-test">
        <livewire:mailcoach::send-test mailer="{{ $mailer->configName() }}" />
    </x-mailcoach::modal>
</div>
