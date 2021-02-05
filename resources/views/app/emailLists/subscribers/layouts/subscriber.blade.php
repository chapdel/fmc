@extends('mailcoach::app.emailLists.layouts.emailList', [
    'title' => $subscriber->email,
    'subTitle' => __('Subscribers')
])

@section('emailList')
    <nav class="tabs">
        <ul>
            <x-mailcoach::navigation-item :href="route('mailcoach.emailLists.subscriber.details', [$subscriber->emailList, $subscriber])">
                {{ __('Profile') }}
            </x-mailcoach::navigation-item>
            <x-mailcoach::navigation-item :href="route('mailcoach.emailLists.subscriber.receivedCampaigns', [$subscriber->emailList, $subscriber])">
                <x-mailcoach::icon-label :text="__('Received campaigns')" invers :count="$totalSendsCount" />
            </x-mailcoach::navigation-item>
        </ul>
    </nav>

    @yield('subscriber')
@endsection
