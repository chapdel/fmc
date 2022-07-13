<div class="card-grid">
    @if ($mail->sent_to_number_of_subscribers)
        <x-mailcoach::success>
            <div>
                {{ __('mailcoach - AutomationMail') }}
                <strong>{{ $mail->name }}</strong>
                {{ __('mailcoach - was delivered to') }}
                <strong>{{ number_format($mail->sent_to_number_of_subscribers - ($failedSendsCount ?? 0)) }} {{ trans_choice('mailcoach - subscriber|subscribers', $mail->sent_to_number_of_subscribers) }}</strong>
            </div>
        </x-mailcoach::success>
    @else
        <x-mailcoach::warning>
            <div>
                {{ __('mailcoach - AutomationMail') }}
                <strong>{{ $mail->name }}</strong>
                {{ __('mailcoach - has not been sent yet.') }}
            </div>
        </x-mailcoach::warning>
    @endif

    @if($failedSendsCount)
        <x-mailcoach::error>
            <div>
                {{ __('mailcoach - Delivery failed for') }}
                <strong>{{ $failedSendsCount }}</strong> {{ trans_choice('mailcoach - subscriber|subscribers', $failedSendsCount) }}
                .
                <a class="underline"
                   href="{{ route('mailcoach.automations.mails.outbox', $mail) . '?filter[type]=failed' }}">{{ __('mailcoach - Check the outbox') }}</a>.
            </div>
        </x-mailcoach::error>
    @endif

    <x-mailcoach::card>
        @include('mailcoach::app.automations.mails.partials.statistics')
    </x-mailcoach::card>
</div>
