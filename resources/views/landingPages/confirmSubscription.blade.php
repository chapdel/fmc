@extends('mailcoach::landingPages.layouts.landingPage', ['title' => __('mailcoach - Confirm subscription')])

@section('landing')
    <p>
        {{ __('mailcoach - Hey, is that really you?') }}
    </p>
    <p class="mt-4">
        {{ __("mailcoach - We've sent you an email to confirm your subscription.") }}
    </p>
@endsection
