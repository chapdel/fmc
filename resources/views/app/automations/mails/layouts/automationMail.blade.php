<x-mailcoach::layout
    :originTitle="$originTitle ?? $mail->name"
    :originHref="$originHref ?? null"
    :title="$title ?? null"
>
    <x-slot name="nav">
        <x-mailcoach::navigation :title="$mail->name">
            <x-mailcoach::navigation-group :href="route('mailcoach.automations.mails.summary', $mail)" :title="__('mailcoach - Performance')">
                <x-mailcoach::navigation-item :href="route('mailcoach.automations.mails.opens', $mail)" data-dirty-warn>
                    {{ __('mailcoach - Opens') }}
                </x-mailcoach::navigation-item>
                <x-mailcoach::navigation-item :href="route('mailcoach.automations.mails.clicks', $mail)" data-dirty-warn>
                    {{ __('mailcoach - Clicks') }}
                </x-mailcoach::navigation-item>
                <x-mailcoach::navigation-item :href="route('mailcoach.automations.mails.unsubscribes', $mail)" data-dirty-warn>
                    {{ __('mailcoach - Unsubscribes') }}
                </x-mailcoach::navigation-item>
                <x-mailcoach::navigation-item :href="route('mailcoach.automations.mails.outbox', $mail)" data-dirty-warn>
                    {{ __('mailcoach - Outbox') }}
                </x-mailcoach::navigation-item>
            </x-mailcoach::navigation-group>

            <x-mailcoach::navigation-item :href="route('mailcoach.automations.mails.settings', $mail)" data-dirty-warn>
                {{ __('mailcoach - Settings') }}
            </x-mailcoach::navigation-item>
            <x-mailcoach::navigation-item :href="route('mailcoach.automations.mails.content', $mail)" data-dirty-warn>
                {{ __('mailcoach - Content') }}
            </x-mailcoach::navigation-item>
            <x-mailcoach::navigation-item :href="route('mailcoach.automations.mails.delivery', $mail)" data-dirty-warn>
                {{ __('mailcoach - Delivery') }}
            </x-mailcoach::navigation-item>
        </x-mailcoach::navigation>
    </x-slot>

    {{ $slot }}
</x-mailcoach::layout>
