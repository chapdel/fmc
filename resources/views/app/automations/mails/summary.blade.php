<div>
    @if ($mail->sent_to_number_of_subscribers)
        <div class="grid grid-cols-auto-1fr gap-2 alert alert-success">
            <div>
                <i class="fas fa-check"></i>
            </div>
            <div>
                {{ __('mailcoach - AutomationMail') }}
                <strong>{{ $mail->name }}</strong>
                {{ __('mailcoach - was delivered to') }}
                <strong>{{ number_format($mail->sent_to_number_of_subscribers - ($failedSendsCount ?? 0)) }} {{ trans_choice('mailcoach - subscriber|subscribers', $mail->sent_to_number_of_subscribers) }}</strong>
            </div>
        </div>
    @else
        <div class="grid grid-cols-auto-1fr gap-2 alert alert-info">
            <div>
                <i class="fas fa-clock"></i>
            </div>
            <div>
                {{ __('mailcoach - AutomationMail') }}
                <strong>{{ $mail->name }}</strong>
                {{ __('mailcoach - has not been sent yet.') }}
            </div>
        </div>
    @endif

    @if($failedSendsCount)
        <div class="grid grid-cols-auto-1fr gap-2 alert alert-success">
            <div>
                <i class="fas fa-times text-red-500"></i>
            </div>
            <div>
                {{ __('mailcoach - Delivery failed for') }}
                <strong>{{ $failedSendsCount }}</strong> {{ trans_choice('mailcoach - subscriber|subscribers', $failedSendsCount) }}
                .
                <a class="underline"
                   href="{{ route('mailcoach.automations.mails.outbox', $mail) . '?filter[type]=failed' }}">{{ __('mailcoach - Check the outbox') }}</a>.
            </div>
        </div>
    @endif

    @include('mailcoach::app.automations.mails.partials.statistics')
</div>
