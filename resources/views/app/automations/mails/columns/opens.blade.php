@php($mail = $getRecord())
<div class="fi-ta-text-item inline-flex flex-col justify-center gap-1.5 text-sm">
    @if($mail->open_rate)
        {{ number_format($mail->unique_open_count) }}
        <div class="td-secondary-line">{{ $mail->open_rate / 100 }}%</div>
    @else
        &ndash;
    @endif
</div>
