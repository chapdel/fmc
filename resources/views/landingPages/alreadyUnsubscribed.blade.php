@extends('mailcoach::landingPages.layouts.landingPage', ['title' => __('mailcoach - Already unsubscribed')])

@section('landing')
    <p>
        {!! __('mailcoach - You were already unsubscribed from the list <strong class="font-semibold">:emailListName</strong>.', ['emailListName' => $emailList->name]) !!}
    </p>
@endsection
