<div class="mt-6 grid grid-cols-3 gap-6 justify-start items-end max-w-xl">
    @if ($campaign->track_opens)
        <x-mailcoach::statistic :href="route('mailcoach.campaigns.opens', $campaign)" class="col-start-1"
                     numClass="text-4xl font-semibold" :stat="number_format($campaign->unique_open_count)" :label="__('mailcoach - Unique Opens')"/>
        <x-mailcoach::statistic :stat="number_format($campaign->open_count)" :label="__('mailcoach - Opens')"/>
        <x-mailcoach::statistic :stat="$campaign->open_rate / 100" :label="__('mailcoach - Open Rate')" suffix="%"/>
    @else
        <div class="col-start-1 col-span-3">
            <div class="text-4xl font-semibold">–</div>
            <div class="text-sm">{{ __('mailcoach - Opens not tracked') }}</div>
        </div>
    @endif

    @if($campaign->track_clicks)
        <x-mailcoach::statistic :href="route('mailcoach.campaigns.clicks', $campaign)" class="col-start-1"
                     numClass="text-4xl font-semibold" :stat="number_format($campaign->unique_click_count)" :label="__('mailcoach - Unique Clicks')"/>
        <x-mailcoach::statistic :stat="number_format($campaign->click_count)" :label="__('mailcoach - Clicks')"/>
        <x-mailcoach::statistic :stat="$campaign->click_rate / 100" :label="__('mailcoach - Click Rate')" suffix="%"/>
    @else
        <div class="col-start-1 col-span-3">
            <div class="text-4xl font-semibold">–</div>
            <div class="text-sm">{{ __('mailcoach - Clicks not tracked') }}</div>
        </div>
    @endif

    <x-mailcoach::statistic :href="route('mailcoach.campaigns.unsubscribes', $campaign)" numClass="text-4xl font-semibold"
                 :stat="number_format($campaign->unsubscribe_count)" :label="__('mailcoach - Unsubscribes')"/>
    <x-mailcoach::statistic :stat="$campaign->unsubscribe_rate / 100" :label="__('mailcoach - Unsubscribe Rate')" suffix="%"/>

    <x-mailcoach::statistic :href="route('mailcoach.campaigns.outbox', $campaign) . '?filter[type]=bounced'"
                 class="col-start-1" numClass="text-4xl font-semibold" :stat="number_format($campaign->bounce_count)"
                 :label="__('mailcoach - Bounces')"/>
    <x-mailcoach::statistic :stat="$campaign->bounce_rate / 100" :label="__('mailcoach - Bounce Rate')" suffix="%"/>
</div>
