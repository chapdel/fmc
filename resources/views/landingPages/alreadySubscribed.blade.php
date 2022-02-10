@extends('mailcoach::landingPages.layouts.landingPage', ['title' => __('mailcoach - Already subscribed')])

@section('landing')
    <p>
        {{ __('mailcoach - You are a real fan!') }}
    </p>
    <p class="mt-4">
        {{ __('mailcoach - You were already subscribed to this list.') }}
    </p>
@endsection
