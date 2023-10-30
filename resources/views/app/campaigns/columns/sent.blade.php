@php($campaign = $getRecord())
<div class="fi-ta-text-item inline-flex flex-col justify-center gap-1.5 px-3 tabular-nums">
    @if($campaign->isSent())
        {{ optional($campaign->sent_at)->toMailcoachFormat() }}
    @elseif($campaign->isSending())
        {{ optional($campaign->updated_at)->toMailcoachFormat() }}
        <div class="td-secondary-line">
            {{ __mc('In progress') }}
        </div>
    @elseif($campaign->isScheduled())
        {{ optional($campaign->scheduled_at)->toMailcoachFormat() }}
        <div class="td-secondary-line">
            {{ __mc('Scheduled') }}
        </div>
    @else
        &ndash;
    @endif
</div>
