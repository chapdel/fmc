@extends('mailcoach::app.emailLists.layouts.edit', ['emailList' => $emailList])

@section('breadcrumbs')
    <li><span class="breadcrumb">{{ $emailList->name }}</span></li>
@endsection

@section('emailList')
    @include('mailcoach::app.emailLists.partials.chart')

    <hr class="border-t-2 border-gray-200 my-8">

    <h2 class="markup-h2">{{ __('Statistics') }}</h2>

    <div class="mt-6 grid grid-cols-4 gap-6 justify-start items-end">
        <x-mailcoach::statistic :href="route('mailcoach.emailLists.subscribers', $emailList)" class="col-start-1"
                                numClass="text-4xl font-semibold" :stat="number_format($totalSubscriptionsCount)" :label="__('Subscribers')"/>
        <x-mailcoach::statistic :href="route('mailcoach.emailLists.subscribers', $emailList)"
                                numClass="text-4xl font-semibold" :stat="number_format($startSubscriptionsCount)" :label="__('Subscribers (30 days)')"/>
        <x-mailcoach::statistic :stat="$growthRate" :label="__('Growth Rate')" suffix="%"/>
        <div></div>
        <x-mailcoach::statistic :href="route('mailcoach.emailLists.subscribers', $emailList) . '?filter[status]=unsubscribed'" class="col-start-1"
                                numClass="text-4xl font-semibold" :stat="number_format($totalUnsubscribeCount)" :label="__('Unsubscribes')"/>
        <x-mailcoach::statistic :href="route('mailcoach.emailLists.subscribers', $emailList)  . '?filter[status]=unsubscribed'"
                                numClass="text-4xl font-semibold" :stat="number_format($startUnsubscribeCount)" :label="__('Unsubscribes (30 days)')"/>
        <x-mailcoach::statistic :stat="$churnRate" :label="__('Churn Rate')" suffix="%"/>
        <div></div>
        <x-mailcoach::statistic :stat="$averageOpenRate" :label="__('Average Open Rate')" suffix="%"/>
        <x-mailcoach::statistic :stat="$averageClickRate" :label="__('Average Click Rate')" suffix="%"/>
        <x-mailcoach::statistic :stat="$averageUnsubscribeRate" :label="__('Average Unsubscribe Rate')" suffix="%"/>
        <x-mailcoach::statistic :stat="$averageBounceRate" :label="__('Average Bounce Rate')" suffix="%"/>
    </div>
@endsection
