@php($campaign = $getRecord())
<div class="fi-ta-text-item inline-flex items-center gap-1.5 text-sm">
    @if (! $campaign->isCancelled() && $campaign->contentItem->sent_to_number_of_subscribers)
        {{ number_format($campaign->contentItem->sent_to_number_of_subscribers) }}
    @elseif ($campaign->contentItem->sent_sends_count)
        {{ number_format($campaign->contentItem->sent_sends_count) }}
    @else
        &ndash;
    @endif
</div>
