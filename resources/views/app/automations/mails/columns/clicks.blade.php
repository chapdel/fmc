@php($mail = $getRecord())
<div class="fi-ta-text-item inline-flex flex-col justify-center gap-1.5 text-sm">
    @if($mail->contentItem->click_rate)
        {{ number_format($mail->contentItem->unique_click_count) }}
        <div class="td-secondary-line">{{ $mail->contentItem->click_rate / 100 }}%</div>
    @else
        &ndash;
    @endif
</div>
