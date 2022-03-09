@extends('mailcoach::landingPages.layouts.landingPage', ['title' => __('mailcoach - Unsubscribed')])

@section('landing')
    <p class="mt-4">
        {!! __('mailcoach - Are you sure you want to unsubscribe from list <strong class="font-semibold">:emailListName</strong>\'s tag :tag?', ['emailListName' => $subscriber->emailList->name, 'tag' => $tag]) !!}
    </p>

    <div class="mt-4">
        <form method="POST">
            @csrf
            <button class="button bg-red-400 shadow" id="confirmationButton" type="submit">{{__('mailcoach - Unsubscribe') }}</button>
        </form>
    </div>

    @if (is_null($send) || $send->created_at->isBefore(now()->subMinutes(5)))
        <script>
            document.getElementById("confirmationButton").click();
        </script>
    @endif
@endsection
