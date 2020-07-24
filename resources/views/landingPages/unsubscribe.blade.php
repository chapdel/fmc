@extends('mailcoach::landingPages.layouts.landingPage', ['title' => __('Unsubscribed')])

@section('content')
    <p>
        {{ __('Do you want to unsubscribe?') }}
    </p>
    <p class="mt-4">
        {!! __('Are you sure you want to unsubscribe from list <strong class="font-semibold">:emailListName</strong>.', ['emailListName' => $subscriber->emailList->name]) !!}
    </p>
    <div>
        <form method="POST">
            @csrf

            <button id="confirmationButton" type="submit">{{ !! __('Confirm unsubscribe') }}</button>
        </form>
    </div>

    <script>
        document.getElementById("confirmationButton").click();
    </script>
@endsection
