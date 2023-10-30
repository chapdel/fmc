@php(
    /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign $campaign */
    $campaign = $getRecord()
)

<div class="fi-ta-text-item inline-flex flex-col justify-center gap-1.5 px-3">
    @if($campaign->clickCount())
        {{ number_format($campaign->uniqueClickCount()) }}
        <div class="td-secondary-line">{{ $campaign->clickRate() / 100 }}%</div>
    @else
        &ndash;
    @endif
</div>
