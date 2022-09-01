<div class="card-grid" id="campaign-summary" wire:poll.10s>
    <x-mailcoach::card>
    @if((! $campaign->isSent()) || (! $campaign->wasSentToAllSubscribers()))
        @if (! $campaign->sent_to_number_of_subscribers && ! $campaign->isCancelled())
            <div class="progress-bar">
                <div class="progress-bar-value" style="width:0"></div>
            </div>

            <x-mailcoach::help sync full>
                <div class="flex justify-between items-center w-full">
                    <div>
                        {{ __('mailcoach - Campaign') }}
                        <strong>{{ $campaign->name }}</strong>
                        {{ __('mailcoach - is preparing to send to') }}

                        @if($campaign->emailList)
                            <a href="{{ route('mailcoach.emailLists.subscribers', $campaign->emailList) }}">{{ $campaign->emailList->name }}</a>
                        @else
                            &lt;{{ __('mailcoach - deleted list') }}&gt;
                        @endif

                        @if($campaign->usesSegment())
                            ({{ $campaign->segment_description }})
                        @endif
                        ...
                    </div>

                    <x-mailcoach::confirm-button
                        class="ml-auto text-red-500 underline"
                         onConfirm="() => $wire.cancelSending()"
                         :confirm-text="__('mailcoach - Are you sure you want to cancel sending this campaign?')">
                        Cancel
                    </x-mailcoach::confirm-button>
                </div>
            </x-mailcoach::help>
        @elseif ($campaign->isCancelled())
            @if($campaign->sent_to_number_of_subscribers)
                <div class="progress-bar">
                    <div class="progress-bar-value" style="width:{{ ($campaign->sendsCount() / $campaign->sent_to_number_of_subscribers) * 100 }}%"></div>
                </div>
            @endif
            <x-mailcoach::error full>
                <div class="flex justify-between items-center w-full">
                    <p>
                        <span class="inline-block">{{ __('mailcoach - Campaign') }}</span>
                        <strong>{{ $campaign->name }}</strong>

                        {{ __('mailcoach - sending is cancelled.') }}

                        {{ __('mailcoach - It was sent to :sendsCount/:sentToNumberOfSubscribers :subscriber of', [
                            'sendsCount' => number_format($campaign->sendsCount()),
                            'sentToNumberOfSubscribers' => number_format($campaign->sent_to_number_of_subscribers),
                            'subscriber' => trans_choice('mailcoach - subscriber|subscribers', $campaign->sent_to_number_of_subscribers)
                        ]) }}

                        @if($campaign->emailList)
                            <a href="{{ route('mailcoach.emailLists.subscribers', $campaign->emailList) }}">{{ $campaign->emailList->name }}</a>
                        @else
                            &lt;{{ __('mailcoach - deleted list') }}&gt;
                        @endif

                        @if($campaign->usesSegment())
                            ({{ $campaign->segment_description }})
                        @endif
                    </p>
                </div>
            </x-mailcoach::error>
        @else
            <div class="progress-bar">
                <div class="progress-bar-value" style="width:{{ ($campaign->sendsCount() / $campaign->sent_to_number_of_subscribers) * 100 }}%"></div>
            </div>
            <x-mailcoach::help sync full>
                <div class="flex justify-between items-center w-full">
                    <span class="block">
                        <span class="inline-block">{{ __('mailcoach - Campaign') }}</span>
                        <strong>{{ $campaign->name }}</strong>

                        @if (! $campaign->allSendsCreated() && $campaign->sends()->count() < $campaign->sent_to_number_of_subscribers)
                            <br>
                            {{ __('mailcoach - is preparing :sendsCount/:sentToNumberOfSubscribers :send for', [
                                'sendsCount' => number_format($campaign->sends()->count()),
                                'sentToNumberOfSubscribers' => number_format($campaign->sent_to_number_of_subscribers),
                                'send' => trans_choice(__('mailcoach - send|sends'), $campaign->sent_to_number_of_subscribers)
                            ]) }}
                            @if($campaign->emailList)
                                <a href="{{ route('mailcoach.emailLists.subscribers', $campaign->emailList) }}">{{ $campaign->emailList->name }}</a>
                            @else
                                &lt;{{ __('mailcoach - deleted list') }}&gt;
                            @endif
                            @if($campaign->usesSegment())
                                ({{ $campaign->segment_description }})
                            @endif
                            <br>
                        @endif

                        @php($sendsCount = $campaign->sendsCount())
                        @if ($sendsCount === $campaign->sent_to_number_of_subscribers)
                            {{ __('mailcoach - is finishing up') }}
                        @else
                            {{ __('mailcoach - is sending to :sendsCount/:sentToNumberOfSubscribers :subscriber of', [
                                'sendsCount' => number_format($campaign->sendsCount()),
                                'sentToNumberOfSubscribers' => number_format($campaign->sent_to_number_of_subscribers),
                                'subscriber' => trans_choice('mailcoach - subscriber|subscribers', $campaign->sent_to_number_of_subscribers)
                            ]) }}

                            @if($campaign->emailList)
                                <a href="{{ route('mailcoach.emailLists.subscribers', $campaign->emailList) }}">{{ $campaign->emailList->name }}</a>
                            @else
                                &lt;{{ __('mailcoach - deleted list') }}&gt;
                            @endif
                            @if($campaign->usesSegment())
                                ({{ $campaign->segment_description }})
                            @endif
                        @endif
                    </span>

                    <x-mailcoach::confirm-button
                        class="ml-auto text-red-500 underline"
                        onConfirm="() => $wire.cancelSending()"
                        :confirm-text="__('mailcoach - Are you sure you want to cancel sending this campaign?')">
                        Cancel
                    </x-mailcoach::confirm-button>
                </div>
            </x-mailcoach::help>
        @endif
    @else
        <x-mailcoach::success class="md:max-w-full" full>
            <div>
                {{ __('mailcoach - Campaign') }}
                <a target="_blank" href="{{ $campaign->webviewUrl() }}"><strong>{{ $campaign->name }}</strong></a>
                {{ __('mailcoach - was delivered successfully to') }}
                <strong>{{ number_format($campaign->sent_to_number_of_subscribers - ($failedSendsCount ?? 0)) }} {{ trans_choice('mailcoach - subscriber|subscribers', $campaign->sent_to_number_of_subscribers) }}</strong>

                {{ __('mailcoach - of') }}

                @if($campaign->emailList)
                    <a href="{{ route('mailcoach.emailLists.subscribers', $campaign->emailList) }}">{{ $campaign->emailList->name }}</a>
                @else
                    &lt;{{ __('mailcoach - deleted list') }}&gt;
                @endif
                @if($campaign->usesSegment())
                    ({{ $campaign->segment_description }})
                @endif
            </div>

            @if($failedSendsCount)
                <div>
                    <i class="fas fa-times text-red-500"></i>
                </div>
                <div>
                    {{ __('mailcoach - Delivery failed for') }} <strong>{{ number_format($failedSendsCount) }}</strong> {{ trans_choice('mailcoach - subscriber|subscribers', $failedSendsCount) }}.
                    <a class="underline" href="{{ route('mailcoach.campaigns.outbox', $campaign) . '?filter[type]=failed' }}">{{ __('mailcoach - Check the outbox') }}</a>.
                </div>
            @endif

            <div class="text-sm">{{ $campaign->sent_at->toMailcoachFormat() }}</div>
        </x-mailcoach::success>

        @if ($campaign->opens()->count() || $campaign->clicks()->count())
            <livewire:mailcoach::campaign-statistics :campaign="$campaign" />
        @endif
    @endif
    </x-mailcoach::card>

    <x-mailcoach::card>
        <h2 class="markup-h2 mb-0">
            {{ __('mailcoach - Totals') }}
        </h2>
        @include('mailcoach::app.campaigns.partials.statistics')
    </x-mailcoach::card>
</div>
