<div>
    <h1 class="text-xl text-indigo-900/50 -mt-6 mb-4">
        Hi, <strong>{{ str(Auth::user()->name)->ucfirst() }}</strong>
    </h1>
    <div class="grid grid-cols-12 gap-6">
        @if ((new Spatie\Mailcoach\Domain\Shared\Support\License\License())->hasExpired())
            <x-mailcoach::tile class="bg-orange-100" cols="3" icon="credit-card">
                <x-slot:link><a class="underline" href="https://spatie.be/products/mailcoach" data-turbo="false">Renew license</a></x-slot:link>
                Your Mailcoach license has expired. <a class="underline" href="https://spatie.be/products/mailcoach">Renew your license</a> and benefit from fixes and new features.
            </x-mailcoach::tile>
        @endif

        @include('mailcoach::app.layouts.partials.beforeDashboardTiles')
        
        <x-mailcoach::tile cols="2" icon="users">
            <x-slot:link><span class="text-sm">30 days</span></x-slot:link>
            <h2 class="dashboard-title">
                Audience
            </h2>
            <div class="flex flex-col">
                <span class="dashboard-value">{{ $this->abbreviateNumber($recentSubscribers) }}</span>
                <span class="dashboard-label">Recent Subscribers</span>
            </div>
        </x-mailcoach::tile>

        <x-mailcoach::tile class="" cols="4" icon="envelope-open">
            <x-slot:link><a href="{{ route('mailcoach.campaigns') }}">Campaigns</a></x-slot:link>

            <h2 class="dashboard-title">
                @if ($totalCount = $this->getCampaignClass()::count())
                    {{ $this->abbreviateNumber($totalCount) }} Campaigns
                @else
                    Create your first campaign
                @endif
            </h2>
            <div class="flex justify-between">
                @if ($draftCount = $this->getCampaignClass()::draft()->count())
                    <a href="{{ route('mailcoach.campaigns') }}?status=draft" class="flex flex-col">
                        <span class="dashboard-value">{{ $this->abbreviateNumber($draftCount) }}</span>
                        <span class="dashboard-label">Draft</span>
                    </a>
                @endif

                @if ($scheduledCount = $this->getCampaignClass()::scheduled()->count())
                    <a href="{{ route('mailcoach.campaigns') }}?status=scheduled" class="flex flex-col">
                        <span class="dashboard-value">{{ $this->abbreviateNumber($scheduledCount) }}</span>
                        <span class="dashboard-label">Scheduled</span>
                    </a>
                @endif

                @if ($sentCount = $this->getCampaignClass()::sent()->count())
                    <a href="{{ route('mailcoach.campaigns') }}?status=sent" class="flex flex-col">
                        <span class="dashboard-value">{{ $this->abbreviateNumber($sentCount) }}</span>
                        <span class="dashboard-label">Sent</span>
                    </a>
                @endif
            </div>
        </x-mailcoach::tile>

        @if ($latestCampaign)
            <x-mailcoach::tile class="" cols="4" icon="paper-plane">
                <x-slot:link><a href="{{ route('mailcoach.campaigns.summary', $latestCampaign) }}">{{ $latestCampaign->name }}</a></x-slot:link>
                
                <h2 class="dashboard-title">{{ $latestCampaign->name }}</h2>
                <a href="{{ route('mailcoach.campaigns.summary', $latestCampaign) }}" class="block">
                    <div class="flex justify-between">
                        <div class="flex flex-col">
                            <span class="dashboard-value">{{ $this->abbreviateNumber($latestCampaign->unique_open_count) }}</span>
                            <span class="dashboard-label">Opens</span>
                        </div>

                        <div class="flex flex-col">
                            <span class="dashboard-value">{{ $this->abbreviateNumber($latestCampaign->unique_click_count) }}</span>
                            <span class="dashboard-label">Clicks</span>
                        </div>

                        <div class="flex flex-col">
                            <span class="dashboard-value">{{ $this->abbreviateNumber($latestCampaign->unsubscribe_count) }}</span>
                            <span class="dashboard-label">Unsubscribes</span>
                        </div>

                        <div class="flex flex-col">
                            <span class="dashboard-value">{{ $this->abbreviateNumber($latestCampaign->bounce_count) }}</span>
                            <span class="dashboard-label">Bounces</span>
                        </div>

                    </div>
                </a>
            </x-mailcoach::tile>
        @endif

        @include('mailcoach::app.layouts.partials.beforeDashboardGraph')

        <div class="col-span-12">
            <livewire:mailcoach::dashboard-chart />
        </div>

        @include('mailcoach::app.layouts.partials.afterDashboardGraph')
    </div>
</div>
