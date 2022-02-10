@extends('mailcoach::landingPages.layouts.landingPage', ['title' => __('mailcoach - Subscribed')])

@section('landing')
    <p>
        {{ __('mailcoach - Happy to have you!') }}
    </p>
    <p class="mt-4">
        @isset($subscriber)
            {!! __('mailcoach - You are now subscribed to the list <strong class="font-semibold">:emailListName</strong>.', ['emailListName' => $subscriber->emailList->name]) !!}
        @else
            {!! __('mailcoach - You are now subscribed to the list <strong class="font-semibold">our dummy mailing list</strong>.') !!}
        @endif
    </p>
@endsection

