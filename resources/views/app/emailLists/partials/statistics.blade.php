<div>
    <div class="flex items-center mb-8">
        <x-mailcoach::date-field
            data-min-date=""
            data-max-date="{{ $end }}"
            data-position="auto"
            name="start"
            wire:model="start"
        />
        <span class="mx-2">&mdash;</span>
        <x-mailcoach::date-field
            data-min-date="{{ $start }}"
            data-max-date="{{ now()->format('Y-m-d') }}"
            data-position="auto"
            name="end"
            wire:model="end"
        />
    </div>
    <div x-data="emailListStatisticsChart" x-init="renderChart({
        labels: @js($stats->pluck('label')->values()->toArray()),
        subscribers: @js($stats->pluck('subscribers')->values()->toArray()),
        subscribes: @js($stats->pluck('subscribes')->values()->toArray()),
        unsubscribes: @js($stats->pluck('unsubscribes')->values()->toArray()),
    })">
        <canvas id="chart" style="position: relative; max-height:300px; width:100%; max-width: 100%;"></canvas>
    </div>
    <div class="text-right">
        <small class="text-gray-400">You can drag the chart to zoom.</small>
    </div>

    <h1 class="markup-h1 mt-16">
        {{ __('mailcoach - Totals') }}
    </h1>

    <div class="mt-10 grid grid-cols-4 gap-6 justify-start items-end">
        <x-mailcoach::statistic :href="route('mailcoach.emailLists.subscribers', $emailList)" class="col-start-1"
                                numClass="text-4xl font-semibold" :stat="number_format($totalSubscriptionsCount)" :label="__('mailcoach - Subscribers')"/>
        <x-mailcoach::statistic :href="route('mailcoach.emailLists.subscribers', $emailList)"
                                numClass="text-4xl font-semibold" :stat="number_format($startSubscriptionsCount)" :label="__('mailcoach - Subscribers (30 days)')"/>
        <x-mailcoach::statistic :stat="$growthRate" :label="__('mailcoach - Growth Rate')" suffix="%"/>
        <div></div>
        <x-mailcoach::statistic :href="route('mailcoach.emailLists.subscribers', $emailList) . '?filter[status]=unsubscribed'" class="col-start-1"
                                numClass="text-4xl font-semibold" :stat="number_format($totalUnsubscribeCount)" :label="__('mailcoach - Unsubscribes')"/>
        <x-mailcoach::statistic :href="route('mailcoach.emailLists.subscribers', $emailList)  . '?filter[status]=unsubscribed'"
                                numClass="text-4xl font-semibold" :stat="number_format($startUnsubscribeCount)" :label="__('mailcoach - Unsubscribes (30 days)')"/>
        <x-mailcoach::statistic :stat="$churnRate" :label="__('mailcoach - Churn Rate')" suffix="%"/>
        <div></div>
        <x-mailcoach::statistic :stat="number_format($averageOpenRate, 2)" :label="__('mailcoach - Average Open Rate')" suffix="%"/>
        <x-mailcoach::statistic :stat="number_format($averageClickRate, 2)" :label="__('mailcoach - Average Click Rate')" suffix="%"/>
        <x-mailcoach::statistic :stat="number_format($averageUnsubscribeRate, 2)" :label="__('mailcoach - Average Unsubscribe Rate')" suffix="%"/>
        <x-mailcoach::statistic :stat="number_format($averageBounceRate, 2)" :label="__('mailcoach - Average Bounce Rate')" suffix="%"/>
    </div>
</div>
