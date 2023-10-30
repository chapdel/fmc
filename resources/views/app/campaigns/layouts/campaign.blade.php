<x-mailcoach::layout
    :originTitle="$originTitle ?? $campaign->name"
    :originHref="$originHref ?? null"
    :title="$title ?? null"
>
    <x-slot name="nav">
        <x-mailcoach::navigation :title="$campaign->name">
            @if ($campaign->isSendingOrSent() || $campaign->isCancelled())
                <x-mailcoach::navigation-group :title="__mc('Performance')" :href="route('mailcoach.campaigns.summary', $campaign)">
                    <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.opens', $campaign)">
                        {{ __mc('Opens') }}
                    </x-mailcoach::navigation-item>
                    <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.clicks', $campaign)" :active="Route::is('mailcoach.campaigns.clicks') || Route::is('mailcoach.campaigns.link-clicks')">
                        {{ __mc('Clicks') }}
                    </x-mailcoach::navigation-item>
                    <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.unsubscribes', $campaign)">
                        {{ __mc('Unsubscribes') }}
                    </x-mailcoach::navigation-item>

                    <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.outbox', $campaign)">
                        {{ __mc('Outbox') }}
                    </x-mailcoach::navigation-item>
                </x-mailcoach::navigation-group>
            @endif

            <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.settings', $campaign)">
                {{ __mc('Settings') }}
            </x-mailcoach::navigation-item>
            <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.content', $campaign)">
                {{ __mc('Content') }}
            </x-mailcoach::navigation-item>

            @if (! $campaign->isSendingOrSent() && ! $campaign->isCancelled())
                <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.delivery', $campaign)">
                    {{ __mc('Send') }}
                </x-mailcoach::navigation-item>
            @endif

        </x-mailcoach::navigation>
    </x-slot>

    {{ $slot }}
</x-mailcoach::layout>
