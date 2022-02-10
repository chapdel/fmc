@extends('mailcoach::landingPages.layouts.landingPage', ['title' => __('mailcoach - Unsubscribed')])

@section('landing')
    <p>
        {{ __('mailcoach - Sorry to see you go.') }}
    </p>
    <p class="mt-4">
        {!! __('mailcoach - You have been unsubscribed from list <strong class="font-semibold">:emailListName</strong>.', ['emailListName' => $subscriber->emailList->name]) !!}
    </p>
@endsection
