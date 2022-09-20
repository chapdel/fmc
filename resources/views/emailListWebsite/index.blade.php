<x-mailcoach::layout-website :email-list="$emailList">
    <h1>{{ $emailList->website_title }}</h1>

    @if ($emailList->description)
        {{ $emailList->website_description }}
    @endif

    @if($emailList->show_subscription_form_on_website)
        @include('mailcoach::emailListWebsite.partials.subscription')
    @endif

    @if($campaigns->count() > 0)
        <div>
            <ul>
                @foreach($campaigns as $campaign)
                    <li>
                        <a href="{{ $campaign->websiteUrl() }}">
                            <h2>{{ $campaign->subject }}</h2>
                            <span>
                                {{ $campaign->sent_at->format('Y-m-d') }}
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>

            @if($campaigns->previousPageUrl())
                <a href="{{ $campaigns->previousPageUrl() }}">Newer</a>
            @endif

            @if($campaigns->nextPageUrl())
                <a href="{{ $campaigns->nextPageUrl() }}">Older</a>
            @endif
        </div>
    @else
        No campaigns have been sent yet...
    @endif
</x-mailcoach::layout-website>
