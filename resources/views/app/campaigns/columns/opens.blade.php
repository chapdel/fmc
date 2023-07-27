@php($campaign = $getRecord())
<div class="fi-ta-text-item inline-flex flex-col justify-center gap-1.5 text-sm">
    @if (! $campaign->open_rate)
        &ndash;
    @else
        {{ number_format($campaign->unique_open_count) }}
        <div class="td-secondary-line">{{ $campaign->open_rate / 100 }}%</div>
    @endif
</div>
