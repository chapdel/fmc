<x-mailcoach::layout-list :title="__('mailcoach - Performance')" :emailList="$emailList">

    @include('mailcoach::app.emailLists.partials.chart')

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
</x-mailcoach::layout-list>
