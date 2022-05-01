<x-mailcoach::layout
    :originTitle="$originTitle ?? null"
    :originHref="$originHref ?? null"
    :title="$title ?? null"
>
    <x-slot name="nav">


        <x-mailcoach::navigation>

            @include('mailcoach::app.layouts.partials.beforeFirstMenuItem')

            <x-mailcoach::navigation-group class="lg:hidden">
                <x-mailcoach::navigation-item :href="route('mailcoach.home')">
                        {{ __('mailcoach - Dashboard') }}
                </x-mailcoach::navigation-item>
            </x-mailcoach::navigation-group>

            <x-mailcoach::navigation-group class="lg:hidden sm:col-span-3">
                @include('mailcoach::app.layouts.partials.headerRight')
            </x-mailcoach::navigation-group>

            @can("viewAny", \Spatie\Mailcoach\Domain\Shared\Support\Config::getCampaignClass())
            <x-mailcoach::navigation-group icon="far fa-envelope-open" :title="__('mailcoach - Newsletter')">
                <x-mailcoach::navigation-item :href="route('mailcoach.campaigns')">
                    {{ __('mailcoach - Campaigns') }}
                </x-mailcoach::navigation-item>
                <x-mailcoach::navigation-item :href="route('mailcoach.templates')">
                    {{ __('mailcoach - Templates') }}
                </x-mailcoach::navigation-item>
            </x-mailcoach::navigation-group>
            @endcan

            <x-mailcoach::navigation-group icon="fas fa-magic" :title="__('mailcoach - Drip')">
                <x-mailcoach::navigation-item :href="route('mailcoach.automations')">
                    {{ __('mailcoach - Automations') }}
                </x-mailcoach::navigation-item>
                <x-mailcoach::navigation-item :href="route('mailcoach.automations.mails')">
                    {{ __('mailcoach - Emails') }}
                </x-mailcoach::navigation-item>
            </x-mailcoach::navigation-group>

            @can("viewAny", \Spatie\Mailcoach\Domain\Shared\Support\Config::getEmailListClass())
            <x-mailcoach::navigation-group icon="far fa-address-book" :title="__('mailcoach - Audience')">
                <x-mailcoach::navigation-item :href="route('mailcoach.emailLists')">
                    {{ __('mailcoach - Lists') }}
                </x-mailcoach::navigation-item>
            </x-mailcoach::navigation-group>
            @endcan

            <x-mailcoach::navigation-group icon="far fa-envelope-open" :title="__('mailcoach - Transactional')">
                <x-mailcoach::navigation-item :href="route('mailcoach.transactionalMails')">
                    {{ __('mailcoach - Log') }}
                </x-mailcoach::navigation-item>
                <x-mailcoach::navigation-item :href="route('mailcoach.transactionalMails.templates')">
                    {{ __('mailcoach - Templates') }}
                </x-mailcoach::navigation-item>
            </x-mailcoach::navigation-group>

            @include('mailcoach::app.layouts.partials.afterLastMenuItem')

            <x-mailcoach::navigation-group class="hidden lg:block">
                @include('mailcoach::app.layouts.partials.headerRight')
            </x-mailcoach::navigation-group>
        </x-mailcoach::navigation>

    </x-slot>

    {{ $slot }}
</x-mailcoach::layout>
