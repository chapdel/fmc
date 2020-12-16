<h2 class="markup-h2">{{ __('Statistics') }}</h2>

<div class="mt-6 grid grid-cols-3 gap-6 justify-start items-end max-w-xl">
    @if ($campaign->track_opens)
        <x-statistic :href="route('mailcoach.campaigns.opens', $campaign)" class="col-start-1"
                     numClass="text-4xl font-semibold" :stat="$campaign->unique_open_count" :label="__('Unique Opens')"/>
        <x-statistic :stat="$campaign->open_count" :label="__('Opens')"/>
        <x-statistic :stat="$campaign->open_rate / 100" :label="__('Open Rate')" suffix="%"/>
    @else
        <div class="col-start-1 col-span-3">
            <div class="text-4xl font-semibold">–</div>
            <div class="text-sm">{{ __('Opens not tracked') }}</div>
        </div>
    @endif

    @if($campaign->track_clicks)
        <x-statistic :href="route('mailcoach.campaigns.clicks', $campaign)" class="col-start-1"
                     numClass="text-4xl font-semibold" :stat="$campaign->unique_click_count" :label="__('Unique Clicks')"/>
        <x-statistic :stat="$campaign->click_count" :label="__('Clicks')"/>
        <x-statistic :stat="$campaign->click_rate / 100" :label="__('Click Rate')" suffix="%"/>
    @else
        <div class="col-start-1 col-span-3">
            <div class="text-4xl font-semibold">–</div>
            <div class="text-sm">{{ __('Clicks not tracked') }}</div>
        </div>
    @endif

    <x-statistic :href="route('mailcoach.campaigns.unsubscribes', $campaign)" numClass="text-4xl font-semibold"
                 :stat="$campaign->unsubscribe_count" :label="__('Unsubscribes')"/>
    <x-statistic :stat="$campaign->unsubscribe_rate / 100" :label="__('Unsubscribe Rate')" suffix="%"/>

    <x-statistic :href="route('mailcoach.campaigns.outbox', $campaign) . '?filter[type]=bounced'"
                 class="col-start-1" numClass="text-4xl font-semibold" :stat="$campaign->bounce_count"
                 :label="__('Bounces')"/>
    <x-statistic :stat="$campaign->bounce_rate / 100" :label="__('Bounce Rate')" suffix="%"/>
</div>
