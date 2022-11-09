<?php /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign $campaign */ ?>
<x-mailcoach::layout-website
    :email-list="$emailList"
>
    <section class="index">
        @foreach($campaigns as $campaign)
            <a href="{{ $campaign->websiteUrl() }}" class="card">
                <article class="card-contents">
                    <h2>
                        {{ $campaign->subject }}
                    </h2>
                    <time datetime="{{ $campaign->sent_at }}">
                        {{ $campaign->sent_at->format('F j, Y') }}
                    </time>
                    @if ($summary = $campaign->websiteSummary())
                        <p>{{ $summary }}</p>
                    @endif
                </article>
            </a>
        @endforeach
    </section>
    <nav class="pagination">
        @if($campaigns->previousPageUrl())
            <a href="{{ $campaigns->previousPageUrl() }}">
                Newer
            </a>
        @endif
        @if($campaigns->nextPageUrl())
            <a href="{{ $campaigns->nextPageUrl() }}">
                Older
            </a>
        @endif
    </nav>
</x-mailcoach::layout-website>
