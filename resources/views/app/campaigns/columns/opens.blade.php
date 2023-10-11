@php(
    /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign $campaign */
    $campaign = $getRecord()
)
<div class="fi-ta-text-item inline-flex flex-col justify-center gap-1.5 text-sm">
    @if (! $campaign->openCount())
        &ndash;
    @else
        {{ number_format($campaign->uniqueOpenCount()) }}
        <div class="td-secondary-line">{{ $campaign->openRate() / 100 }}%</div>
    @endif
</div>
