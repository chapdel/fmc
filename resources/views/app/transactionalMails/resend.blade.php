<div>
    @if($transactionalMail->opens->count())
        <x-mailcoach::warning>{{ __('mailcoach - This mail has already been opened, are you sure you want to resend it?') }}</x-mailcoach::warning>
    @else
        <x-mailcoach::help>{{ __('mailcoach - This mail hasn\'t been opened yet.') }}</x-mailcoach::help>
    @endif

    <x-mailcoach::button :label="__('mailcoach - Resend')" class="mt-4 button" wire:click.prevent="resend" />
</div>
