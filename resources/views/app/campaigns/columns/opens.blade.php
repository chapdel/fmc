@php($campaign = $getRecord())
<div class="fi-ta-text-item inline-flex flex-col justify-center gap-1.5 text-sm">
    @if (! $campaign->contentItem->open_rate)
        &ndash;
    @else
        {{ number_format($campaign->contentItem->unique_open_count) }}
        <div class="td-secondary-line">{{ $campaign->contentItem->open_rate / 100 }}%</div>
    @endif
</div>
