<?php
    /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign $campaign */
?>
<x-mailcoach::layout-campaign :title="__('mailcoach - Performance')" :campaign="$campaign">
    <div @if(!$campaign->sent_at || $campaign->sent_at->addDay()->isFuture()) id="campaign-summary" data-poll @endif>
        @if((! $campaign->isSent()) || (! $campaign->wasSentToAllSubscribers()))
            @if (! $campaign->sent_to_number_of_subscribers)
                <div class="progress-bar">
                    <div class="progress-bar-value" style="width:0"></div>
                </div>

                <div class="mt-4 flex alert alert-info">
                    <div class="mr-2">
                        <i class="fas fa-sync fa-spin text-blue-500"></i>
                    </div>
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
                </div>
            @elseif ($campaign->isCancelled())
                <div class="progress-bar">
                    <div class="progress-bar-value" style="width:{{ ($campaign->sendsCount() / $campaign->sent_to_number_of_subscribers) * 100 }}%"></div>
                </div>
                <div class="mt-4 flex alert alert-info">
                    <div class="mr-2">
                        <i class="fas fa-ban text-red-500"></i>
                    </div>
                    <div class="flex justify-between items-center w-full">
                        <p>
                            <span class="inline-block">{{ __('mailcoach - Campaign') }}</span>
                            <strong>{{ $campaign->name }}</strong>

                            {{ __('mailcoach - sending is cancelled.', [
                                'sendsCount' => $campaign->sendsCount(),
                                'sentToNumberOfSubscribers' => $campaign->sent_to_number_of_subscribers,
                                'subscriber' => trans_choice(__('mailcoach - subscriber|subscribers'), $campaign->sent_to_number_of_subscribers)
                            ]) }}

                            {{ __('mailcoach - It was sent to :sendsCount/:sentToNumberOfSubscribers :subscriber of', [
                                'sendsCount' => $campaign->sendsCount(),
                                'sentToNumberOfSubscribers' => $campaign->sent_to_number_of_subscribers,
                                'subscriber' => trans_choice(__('mailcoach - subscriber|subscribers'), $campaign->sent_to_number_of_subscribers)
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
                </div>
            @elseif (! $campaign->allSendsCreated() && $campaign->sends()->count() < $campaign->sent_to_number_of_subscribers)
                <div class="progress-bar">
                    <div class="progress-bar-value" style="width:{{ ($campaign->sends()->count() / $campaign->sent_to_number_of_subscribers) * 100 }}%"></div>
                </div>
                <div class="mt-4 flex alert alert-info">
                    <div class="mr-2">
                        <i class="fas fa-sync fa-spin text-blue-500"></i>
                    </div>
                    <div class="flex justify-between items-center w-full">
                        <p>
                            <span class="inline-block">{{ __('mailcoach - Campaign') }}</span>
                            <strong>{{ $campaign->name }}</strong>

                            {{ __('mailcoach - is creating :sendsCount/:sentToNumberOfSubscribers :send for', [
                                'sendsCount' => $campaign->sends()->count(),
                                'sentToNumberOfSubscribers' => $campaign->sent_to_number_of_subscribers,
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
                        </p>

                        <x-mailcoach::form-button class="text-red-500 underline" action="{{ route('mailcoach.campaigns.cancel-sending', $campaign) }}" dataConfirm dataConfirmText="{{ __('mailcoach - Are you sure you want to cancel sending this campaign?') }}">Cancel</x-mailcoach::form-button>
                    </div>
                </div>
            @else
                <div class="progress-bar">
                    <div class="progress-bar-value" style="width:{{ ($campaign->sendsCount() / $campaign->sent_to_number_of_subscribers) * 100 }}%"></div>
                </div>
                <div class="mt-4 flex alert alert-info">
                    <div class="mr-2">
                        <i class="fas fa-sync fa-spin text-blue-500"></i>
                    </div>
                    <div class="flex justify-between items-center w-full">
                        <p>
                            <span class="inline-block">{{ __('mailcoach - Campaign') }}</span>
                            <strong>{{ $campaign->name }}</strong>

                            {{ __('mailcoach - is sending to :sendsCount/:sentToNumberOfSubscribers :subscriber of', [
                                'sendsCount' => $campaign->sendsCount(),
                                'sentToNumberOfSubscribers' => $campaign->sent_to_number_of_subscribers,
                                'subscriber' => trans_choice(__('mailcoach - subscriber|subscribers'), $campaign->sent_to_number_of_subscribers)
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

                        <x-mailcoach::form-button class="text-red-500 underline" action="{{ route('mailcoach.campaigns.cancel-sending', $campaign) }}" dataConfirm dataConfirmText="{{ __('mailcoach - Are you sure you want to cancel sending this campaign?') }}">Cancel</x-mailcoach::form-button>
                    </div>
                </div>
            @endif
        @else
            <div class="grid grid-cols-auto-1fr gap-2 alert alert-success">
                <div>
                    <i class="fas fa-check text-green-500"></i>
                </div>
                <div>
                    {{ __('mailcoach - Campaign') }}
                    <a target="_blank" href="{{ $campaign->webviewUrl() }}"><strong>{{ $campaign->name }}</strong></a>
                    {{ __('mailcoach - was delivered successfully to') }}
                    <strong>{{ number_format($campaign->sent_to_number_of_subscribers - ($failedSendsCount ?? 0)) }} {{ trans_choice('subscriber|subscribers', $campaign->sent_to_number_of_subscribers) }}</strong>

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
                        {{ __('mailcoach - Delivery failed for') }} <strong>{{ $failedSendsCount }}</strong> {{ trans_choice('subscriber|subscribers', $failedSendsCount) }}.
                        <a class="underline" href="{{ route('mailcoach.campaigns.outbox', $campaign) . '?filter[type]=failed' }}">{{ __('mailcoach - Check the outbox') }}</a>.
                    </div>
                @endif

                <div class="col-start-2 text-sm text-green-600">{{ $campaign->sent_at->toMailcoachFormat() }}</div>
            </div>


            <h2 class="markup-h2 mt-12">{{ __('mailcoach - 24-hour performance') }}</h2>

            <div class="mt-6">
                @include('mailcoach::app.campaigns.partials.chart')
            </div>
        @endif

        @include('mailcoach::app.campaigns.partials.statistics')
    </div>
</x-mailcoach::layout-campaign>
