<div>
@include('mailcoach::app.configuration.mailers.wizards.wizardNavigation')
<x-mailcoach::card>
    <x-mailcoach::help>
        Brevo always enables all tracking on their emails. It will send webhooks to Mailcoach, that will be used to
        automatically unsubscribe people when emails bounce and add open & click tracking.
    </x-mailcoach::help>

    <form class="form-grid" wire:submit="configureSendinblue">
        <x-mailcoach::checkbox-field
            :label="__mc('Enable open tracking')"
            name="trackOpens"
            wire:model.defer="trackOpens"
            disabled
        />

        <x-mailcoach::checkbox-field
            :label="__mc('Enable click tracking')"
            name="trackClicks"
            wire:model.defer="trackClicks"
            disabled
        />

        <x-mailcoach::form-buttons>
            <x-mailcoach::button :label="__mc('Configure Brevo')"/>
        </x-mailcoach::form-buttons>
    </form>
</x-mailcoach::card>
</div>
