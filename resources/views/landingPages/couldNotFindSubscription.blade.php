@extends('mailcoach::landingPages.layouts.landingPage', ['title' => __('mailcoach - Could not find subscription')])

@section('landing')
    <p>
        {{ __('mailcoach - We could not find your subscription to this list.') }}
    </p>
    <p class="mt-4">
        {{ __('mailcoach - The link you used seems invalid.') }}
    </p>
@endsection
