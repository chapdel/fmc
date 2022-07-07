<div>
    <h1 class="markup-h1 -mt-10">
        Hi, {{ str(Auth::user()->name)->ucfirst() }}!
    </h1>
    <div class="grid grid-cols-12 gap-10">
        @if ((new Spatie\Mailcoach\Domain\Shared\Support\License\License())->hasExpired())
            <x-mailcoach::tile class="bg-orange-100" cols="3" icon="credit-card">
                <x-slot:link><a class="underline" href="https://spatie.be/products/mailcoach" data-turbo="false">Renew license</a></x-slot:link>
                Your Mailcoach license has expired. <a class="underline" href="https://spatie.be/products/mailcoach">Renew your license</a> and benefit from fixes and new features.
            </x-mailcoach::tile>
        @endif

        @include('mailcoach::app.layouts.partials.beforeDashboardTiles')
        
        <x-mailcoach::tile cols="2" icon="users">
            <x-slot:link><span class="text-sm">30 days</span></x-slot:link>
            
            <h2 class="markup-h2 mb-0">{{ $this->abbreviateNumber($recentSubscribers) }}</h2>
            <div class="mt-2 text-sm flex">
                <x-mailcoach::icon-label icon="far fa-users" invers text="Recent subscribers"/>
            </div>
        </x-mailcoach::tile>

        <x-mailcoach::tile class="bg-gray-50 text-blue-900" cols="4" icon="envelope-open">
            <x-slot:link><a href="{{ route('mailcoach.campaigns') }}">Campaigns</a></x-slot:link>

            <div class="flex mt-2 justify-between">
                @if ($draftCount = $this->getCampaignClass()::draft()->count())
                    <a href="{{ route('mailcoach.campaigns') }}?status=draft" class="flex flex-col">
                        <span>Draft</span>
                        <span class="text-xl font-bold">{{ $this->abbreviateNumber($draftCount) }}</span>
                    </a>
                @endif

                @if ($scheduledCount = $this->getCampaignClass()::scheduled()->count())
                    <a href="{{ route('mailcoach.campaigns') }}?status=scheduled" class="flex flex-col">
                        <span>Scheduled</span>
                        <span class="text-xl font-bold">{{ $this->abbreviateNumber($scheduledCount) }}</span>
                    </a>
                @endif

                @if ($sentCount = $this->getCampaignClass()::sent()->count())
                    <a href="{{ route('mailcoach.campaigns') }}?status=sent" class="flex flex-col">
                        <span>Sent</span>
                        <span class="text-xl font-bold">{{ $this->abbreviateNumber($sentCount) }}</span>
                    </a>
                @endif

                @if ($totalCount = $this->getCampaignClass()::count())
                    <a href="{{ route('mailcoach.campaigns') }}" class="flex flex-col">
                        <span>Total</span>
                        <span class="text-xl font-bold">{{ $this->abbreviateNumber($totalCount) }}</span>
                    </a>
                @endif
            </div>
        </x-mailcoach::tile>

        @if ($latestCampaign)
            <x-mailcoach::tile class="bg-gray-50 text-blue-900" cols="4" icon="envelope-open">
                <x-slot:link><a href="{{ route('mailcoach.campaigns.summary', $latestCampaign) }}">{{ $latestCampaign->name }}</a></x-slot:link>
                <a href="{{ route('mailcoach.campaigns.summary', $latestCampaign) }}" class="block">
                    <div class="flex mt-2 justify-between">
                        <div class="flex flex-col">
                            <span>Opens</span>
                            <span class="text-xl font-bold">{{ $this->abbreviateNumber($latestCampaign->unique_open_count) }}</span>
                        </div>

                        <div class="flex flex-col">
                            <span>Clicks</span>
                            <span class="text-xl font-bold">{{ $this->abbreviateNumber($latestCampaign->unique_click_count) }}</span>
                        </div>

                        <div class="flex flex-col">
                            <span>Unsubscribes</span>
                            <span class="text-xl font-bold">{{ $this->abbreviateNumber($latestCampaign->unsubscribe_count) }}</span>
                        </div>

                        <div class="flex flex-col">
                            <span>Bounces</span>
                            <span class="text-xl font-bold">{{ $this->abbreviateNumber($latestCampaign->bounce_count) }}</span>
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
