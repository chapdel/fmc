@php($campaign = $getRecord())

<div class="fi-ta-text-item inline-flex flex-col justify-center gap-1.5 text-sm">
    @if($campaign->click_rate)
        {{ number_format($campaign->unique_click_count) }}
        <div class="td-secondary-line">{{ $campaign->click_rate / 100 }}%</div>
    @else
        &ndash;
    @endif
</div>
