<x-mailcoach::layout
    :originTitle="$originTitle ?? $transactionalMail->subject"
    :originHref="$originHref ?? null"
    :title="$title ?? null"
>

     <x-slot name="nav">
        <x-mailcoach::navigation :title="$transactionalMail->subject" :backHref="route('mailcoach.transactionalMails')" :backLabel="__('mailcoach - Log')">
            <x-mailcoach::navigation-group>
                <x-mailcoach::navigation-item :href="route('mailcoach.transactionalMails.show', $transactionalMail)">
                    {{ __('mailcoach - Content') }}
                </x-mailcoach::navigation-item>
                <x-mailcoach::navigation-item :href="route('mailcoach.transactionalMails.performance', $transactionalMail)">
                    {{ __('mailcoach - Performance') }}
                </x-mailcoach::navigation-item>
                <x-mailcoach::navigation-item :href="route('mailcoach.transactionalMails.resend', $transactionalMail)">
                    {{ __('mailcoach - Resend') }}
                </x-mailcoach::navigation-item>
            </x-mailcoach::navigation-group>
        </x-mailcoach::navigation>
    </x-slot>

    {{ $slot }}
</x-mailcoach::layout>
