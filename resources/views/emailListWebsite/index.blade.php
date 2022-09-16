@extends('$mailcoach::emailListWebsite.layouts.emailListWebsite', ['title' => $emailList->website_title)

@section('content')
    <h1>{{ $emailList->website_title }}</h1>

    @if ($emailList->description)
        {{ $emailList->website_description }}
    @endif

    @if($campaigns->count() > 0)
        <div>
            <ul>
            @foreach($campaigns as $campaign)
                <li>
                    <h2>{{ $campaign->subject }}</h2>
                    <span>
                        {{ $campaign->sent_at->format('Y-m-d') }}
                    </span>
                </li>
            @endforeach
            <ul>

            {{ $posts->links() }}
        </div>
    @else
        No campaigns have been sent yet...
    @endif

@endsection
