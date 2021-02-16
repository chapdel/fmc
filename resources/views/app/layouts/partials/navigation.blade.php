<ul class="navigation">

    <li class="opacity-0">|</li>

            @include('mailcoach::app.layouts.partials.beforeFirstMenuItem')

            @can("viewAny", \Spatie\Mailcoach\Domain\Campaign\Models\Campaign::class)
            <x-mailcoach::navigation-dropdown :href="route('mailcoach.campaigns')" icon="far fa-envelope-open" :label="__('Campaigns')">
                <x-mailcoach::navigation-item :href="route('mailcoach.campaigns')">
                    <x-mailcoach::icon-label icon="far fa-fw fa-list-ul" :text="__('Overview')" />
                </x-mailcoach::navigation-item>
                <x-mailcoach::navigation-item :href="route('mailcoach.templates')">
                    <x-mailcoach::icon-label icon="far fa-fw fa-file-code" :text="__('Templates')" />
                </x-mailcoach::navigation-item>
            </x-mailcoach::navigation-dropdown>
            @endcan

            <x-mailcoach::navigation-dropdown :href="route('mailcoach.automations')" icon="far fa-magic" :label="__('Automations')">
                <x-mailcoach::navigation-item :href="route('mailcoach.automations')">
                    <x-mailcoach::icon-label icon="far fa-fw fa-project-diagram" :text="__('Flows')" />
                </x-mailcoach::navigation-item>
                <x-mailcoach::navigation-item href="#">
                    <x-mailcoach::icon-label icon="far fa-fw fa-envelope" :text="__('Mails')" />
                </x-mailcoach::navigation-item>
            </x-mailcoach::navigation-dropdown>


            @can("viewAny", \Spatie\Mailcoach\Domain\Audience\Models\EmailList::class)

            <x-mailcoach::navigation-item :href="route('mailcoach.emailLists')">
                <span class="icon-label">
                    <i class="far fa-address-book"></i>
                    <span class="icon-label-text font-semibold">{{ __('Lists') }}</span>
                </span>
            </x-mailcoach::navigation-item>
            @endcan

            <li class="opacity-0">|</li>

            <x-mailcoach::navigation-dropdown :href="route('mailcoach.transactionalMails')" icon="far fa-exchange-alt" :label="__('Transactional Mails')">
                <x-mailcoach::navigation-item :href="route('mailcoach.transactionalMails')">
                    <x-mailcoach::icon-label icon="far fa-fw fa-list-ul" :text="__('Log')" />
                </x-mailcoach::navigation-item>
                <x-mailcoach::navigation-item :href="route('mailcoach.transactionalMails.templates')">
                    <x-mailcoach::icon-label icon="far fa-fw fa-file-code" :text="__('Templates')" />
                </x-mailcoach::navigation-item>
            </x-mailcoach::navigation-dropdown>

            @include('mailcoach::app.layouts.partials.afterLastMenuItem')

</ul>


