<ul class="navigation">
    <li class="navigation-group">
        <ul class="navigation">
            <x-mailcoach::navigation-item :href="route('mailcoach.home')">
                <span class="icon-label">
                <span 
                class="group w-10 h-10 flex items-center justify-center bg-gradient-to-b from-blue-500 to-blue-600 text-white rounded-full">
                    <span class="w-6 h-6 transform group-hover:scale-90 transition-transform duration-150">
                        @include('mailcoach::app.layouts.partials.logoSvg')
                    </span>
                </span>
                <span class="icon-label-text">Dashboard</span>
                </span>
            </x-mailcoach::navigation-item>
        </ul>
    </li>

    <li class="navigation-group">
        <ul class="navigation">
            @include('mailcoach::app.layouts.partials.beforeFirstMenuItem')
            
            @can("viewAny", \Spatie\Mailcoach\Domain\Campaign\Models\Campaign::class)
            <x-mailcoach::navigation-item :href="route('mailcoach.campaigns')">
                <span class="icon-label">
                    <i class="far fa-envelope-open"></i>
                    <span class="icon-label-text">{{ __('Campaigns') }}</span>
                </span>
            </x-mailcoach::navigation-item>
            @endcan

            <x-mailcoach::navigation-item :href="route('mailcoach.automations')">
                <span class="icon-label">
                    <i class="far fa-magic"></i>
                    <span class="icon-label-text">{{ __('Automations') }}</span>
                </span>
            </x-mailcoach::navigation-item>

            @can("viewAny", \Spatie\Mailcoach\Domain\Campaign\Models\EmailList::class)
            <x-mailcoach::navigation-item :href="route('mailcoach.emailLists')">
                <span class="icon-label">
                    <i class="far fa-address-book"></i>
                    <span class="icon-label-text">{{ __('Lists') }}</span>
                </span>
            </x-mailcoach::navigation-item>
            @endcan
        </ul>
    </li>

    <li class="navigation-group">
        <ul class="navigation">
            <x-mailcoach::navigation-item :href="route('mailcoach.transactionalMails')">
                <span class="icon-label">
                    <i class="far fa-exchange-alt"></i>
                    <span class="icon-label-text">{{ __('Transactional mails') }}</span>
                </span>
            </x-mailcoach::navigation-item>

            @include('mailcoach::app.layouts.partials.afterLastMenuItem')
        </ul>
    </li>
</ul>
