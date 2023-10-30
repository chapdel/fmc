<x-mailcoach::layout
    :originTitle="$originTitle ?? $mail->name"
    :originHref="$originHref ?? null"
    :title="$title ?? null"
>
    <x-slot name="nav">
        <x-mailcoach::navigation :title="$mail->name">
            <x-mailcoach::navigation-group :href="route('mailcoach.automations.mails.summary', $mail)" :title="__mc('Performance')">
                <x-mailcoach::navigation-item :href="route('mailcoach.automations.mails.opens', $mail)">
                    {{ __mc('Opens') }}
                </x-mailcoach::navigation-item>
                <x-mailcoach::navigation-item :href="route('mailcoach.automations.mails.clicks', $mail)">
                    {{ __mc('Clicks') }}
                </x-mailcoach::navigation-item>
                <x-mailcoach::navigation-item :href="route('mailcoach.automations.mails.unsubscribes', $mail)">
                    {{ __mc('Unsubscribes') }}
                </x-mailcoach::navigation-item>
                <x-mailcoach::navigation-item :href="route('mailcoach.automations.mails.outbox', $mail)">
                    {{ __mc('Outbox') }}
                </x-mailcoach::navigation-item>
            </x-mailcoach::navigation-group>

            <x-mailcoach::navigation-item :href="route('mailcoach.automations.mails.settings', $mail)">
                {{ __mc('Settings') }}
            </x-mailcoach::navigation-item>
            <x-mailcoach::navigation-item :href="route('mailcoach.automations.mails.content', $mail)">
                {{ __mc('Content') }}
            </x-mailcoach::navigation-item>
            <x-mailcoach::navigation-item :href="route('mailcoach.automations.mails.delivery', $mail)">
                {{ __mc('Delivery') }}
            </x-mailcoach::navigation-item>
        </x-mailcoach::navigation>
    </x-slot>

    {{ $slot }}
</x-mailcoach::layout>
