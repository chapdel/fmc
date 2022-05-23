<div>
    <h1 class="markup-h1 -mt-6">
        Hello, {{ str(Auth::user()->name)->ucfirst() }}!
    </h1>
    <div class="grid grid-cols-12 gap-6">
        @if ((new Spatie\Mailcoach\Domain\Shared\Support\License\License())->hasExpired())
            <x-mailcoach::tile class="bg-orange-100" cols="3" icon="credit-card">
                <x-slot:link><a class="underline" href="https://spatie.be/products/mailcoach" data-turbo="false">Renew license</a></x-slot:link>
                Your Mailcoach license has expired. <a class="underline" href="https://spatie.be/products/mailcoach">Renew your license</a> and benefit from fixes and new features.
            </x-mailcoach::tile>
        @endif

        <x-mailcoach::tile class="bg-gray-50 text-blue-900" cols="2" icon="users">
            <x-slot:link><span class="text-sm">30 days</span></x-slot:link>
            <div class="flex mt-auto items-center">
                <span>Recent subscribers</span>
                <span class="text-4xl font-bold ml-auto text-blue-900">{{ $this->abbreviateNumber($recentSubscribers) }}</span>
            </div>
        </x-mailcoach::tile>

        <x-mailcoach::tile class="bg-gray-50 text-blue-900" cols="2" icon="envelope-open">
            <a href="{{ route('mailcoach.campaigns') }}" class="flex items-center mt-auto">
                <span>Campaigns</span>
                <span class="text-4xl font-bold ml-auto text-blue-900">{{ $this->abbreviateNumber(self::getCampaignClass()::count()) }}</span>
            </a>
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
