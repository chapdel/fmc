<div class="card-grid">
<x-mailcoach::card>
    <div class="flex gap-4 items-center mb-8">
        <x-mailcoach::date-field
            min-date=""
            max-date="{{ $end }}"
            position="auto"
            name="start"
            wire:model="start"
            label="From"
            class="flex-row gap-0"
            inputClass="w-32"
        />
        <x-mailcoach::date-field
            min-date="{{ $start }}"
            max-date="{{ now()->format('Y-m-d') }}"
            position="auto"
            name="end"
            wire:model="end"
            label="To"
            class="flex-row gap-0"
            inputClass="w-32"
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
    <div class="mt-4 text-right">
        <small class="text-gray-500">You can drag the chart to zoom.</small>
    </div>
</x-mailcoach::card>

<x-mailcoach::card>
    <h2 class="markup-h2">
        {{ __('mailcoach - Totals') }}
    </h2>

    <div class="mt-10 grid grid-cols-4 gap-6 justify-start md:items-end">
        <x-mailcoach::statistic :href="route('mailcoach.emailLists.subscribers', $emailList)" class="col-start-1"
                                numClass="text-4xl font-semibold" :stat="number_format($totalSubscriptionsCount)" :label="__('mailcoach - Subscribers')"/>
        <x-mailcoach::statistic :href="route('mailcoach.emailLists.subscribers', $emailList)"
                                numClass="text-4xl font-semibold" :stat="number_format($totalSubscriptionsCount - $startSubscriptionsCount)" :label="__('mailcoach - Subscribers (daterange)')"/>
        <x-mailcoach::statistic :stat="$growthRate" :label="__('mailcoach - Growth Rate')" suffix="%"/>
        <div></div>
        <x-mailcoach::statistic :href="route('mailcoach.emailLists.subscribers', $emailList) . '?filter[status]=unsubscribed'" class="col-start-1"
                                numClass="text-4xl font-semibold" :stat="number_format($totalUnsubscribeCount)" :label="__('mailcoach - Unsubscribes')"/>
        <x-mailcoach::statistic :href="route('mailcoach.emailLists.subscribers', $emailList)  . '?filter[status]=unsubscribed'"
                                numClass="text-4xl font-semibold" :stat="number_format($startUnsubscribeCount)" :label="__('mailcoach - Unsubscribes (daterange)')"/>
        <x-mailcoach::statistic :stat="$churnRate" :label="__('mailcoach - Churn Rate')" suffix="%"/>
        <div></div>
        <x-mailcoach::statistic :stat="number_format($averageOpenRate, 2)" :label="__('mailcoach - Average Open Rate')" suffix="%"/>
        <x-mailcoach::statistic :stat="number_format($averageClickRate, 2)" :label="__('mailcoach - Average Click Rate')" suffix="%"/>
        <x-mailcoach::statistic :stat="number_format($averageUnsubscribeRate, 2)" :label="__('mailcoach - Average Unsubscribe Rate')" suffix="%"/>
        <x-mailcoach::statistic :stat="number_format($averageBounceRate, 2)" :label="__('mailcoach - Average Bounce Rate')" suffix="%"/>
    </div>
</x-mailcoach::card>
</div>
