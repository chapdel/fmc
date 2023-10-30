<?php /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign $campaign */ ?>
<x-mailcoach::card>
    <div class="grid grid-cols-3 xl:grid-cols-5 gap-12 justify-start items-start">
        <x-mailcoach::statistic
            :href="route('mailcoach.campaigns.outbox', $campaign)"
            :stat="number_format($campaign->sentToNumberOfSubscribers())"
            :label="__mc('Recipients')"
            :progress="$campaign->segmentSubscriberCount()
                ? $campaign->sentToNumberOfSubscribers() / $campaign->segmentSubscriberCount() * 100
                : 100"
        />

        @if ($campaign->openCount())
            <x-mailcoach::statistic
                :href="route('mailcoach.campaigns.opens', $campaign)"
                :stat="$campaign->openRate() / 100"
                :label="__mc('Open Rate')"
                suffix="%"
                :progress="$campaign->openRate() / 100"
                :progress-tooltip="$campaign->uniqueOpenCount()"
            />
        @else
            <div class="">
                <div class="leading-none text-4xl font-semibold">–</div>
                <div class="text-sm">{{ __mc('No opens tracked') }}</div>
            </div>
        @endif

        @if($campaign->clickCount())
            <x-mailcoach::statistic
                :href="route('mailcoach.campaigns.clicks', $campaign)"
                :stat="$campaign->clickRate() / 100"
                :label="__mc('Click Rate')"
                suffix="%"
                :progress="$campaign->clickRate() / 100"
                :progress-tooltip="$campaign->uniqueClickCount()"
            />
        @else
            <div class="">
                <div class="leading-none text-4xl font-semibold">–</div>
                <div class="text-sm">{{ __mc('No clicks tracked') }}</div>
            </div>
        @endif

        <x-mailcoach::statistic
            :href="route('mailcoach.campaigns.unsubscribes', $campaign)"
            :stat="$campaign->unsubscribeRate() / 100"
            :label="__mc('Unsubscribe Rate')"
            suffix="%"
            :progress="$campaign->unsubscribeRate() / 100"
            :progress-tooltip="$campaign->unsubscribeCount()"
            progress-class="bg-red-500"
        />

        <x-mailcoach::statistic
            :href="route('mailcoach.campaigns.outbox', $campaign) . '?filter[type]=bounced'"
            :stat="$campaign->bounceRate() / 100"
            :label="__mc('Bounce Rate')"
            suffix="%"
            :progress="$campaign->bounceRate() / 100"
            :progress-tooltip="$campaign->bounceCount()"
            progress-class="bg-red-500"
        />
    </div>
</x-mailcoach::card>

@if ($campaign->isSplitTested())
    <x-mailcoach::line-title>
        {{ __mc('Split tests') }}
    </x-mailcoach::line-title>
    <div class="grid lg:grid-cols-2 gap-6">
        @foreach ($campaign->contentItems as $index => $contentItem)
            @php($stats = $contentItem->getStatsBefore($campaign->splitTestWinnerDecidedAt()))
            <x-mailcoach::card class="relative overflow-hidden pt-16">
                <div class="absolute flex justify-center w-full top-0 left-0 right-0 pt-4">
                    <div class="mx-auto w-8 h-8 rounded-full inline-flex items-center justify-center text-sm leading-none font-semibold counter-automation">
                        {{ $index + 1 }}
                    </div>
                </div>
                @if ($campaign->splitTestWinner?->id === $contentItem->id)
                    <div class="absolute right-0 top-0 -mt-4 -mr-4 h-16 w-16">
                        <div
                            class="absolute transform rotate-45 bg-green-500 shadow-md text-center font-semibold uppercase tracking-wider text-xs text-white py-1.5 right-[-35px] top-[32px] w-[170px]">
                            {{ __mc('Winner') }}
                        </div>
                    </div>
                @endif
                <h3 class="markup-h3 mb-0">
                    {{ $contentItem->subject }}
                </h3>

                <div class="grid grid-cols-3 gap-6 justify-start items-start">
                    <x-mailcoach::statistic :href="route('mailcoach.campaigns.outbox', $campaign)" :stat="number_format($stats['sent_to_number_of_subscribers'])" :label="__mc('Recipients')"/>

                    @if ($stats['open_count'])
                        <x-mailcoach::statistic
                            class="col-start-1"
                            :href="route('mailcoach.campaigns.opens', $campaign)"
                            :stat="$stats['open_rate'] / 100"
                            :label="__mc('Open Rate')"
                            suffix="%"
                            :progress="$stats['unique_open_count'] / $stats['sent_to_number_of_subscribers'] * 100"
                            :progress-tooltip="$stats['unique_open_count']"
                        />
                    @else
                        <div class="col-start-1 col-span-3">
                            <div class="">–</div>
                            <div class="text-sm">{{ __mc('No opens tracked') }}</div>
                        </div>
                    @endif

                    @if($stats['click_count'])
                        <x-mailcoach::statistic
                            :href="route('mailcoach.campaigns.opens', $campaign)"
                            :stat="$stats['click_rate'] / 100"
                            :label="__mc('Click Rate')"
                            suffix="%"
                            :progress="$stats['unique_click_count'] / $stats['sent_to_number_of_subscribers'] * 100"
                            :progress-tooltip="$stats['unique_click_count']"
                        />
                    @else
                        <div class="col-start-1 col-span-3">
                            <div class="">–</div>
                            <div class="text-sm">{{ __mc('No clicks tracked') }}</div>
                        </div>
                    @endif

                    <x-mailcoach::statistic
                        class="col-start-1"
                        :href="route('mailcoach.campaigns.unsubscribes', $campaign)"
                        :stat="$stats['unsubscribe_rate'] / 100"
                        :label="__mc('Unsubscribe Rate')"
                        suffix="%"
                        :progress="$stats['unsubscribe_rate'] / 100"
                        :progress-tooltip="$stats['unsubscribe_count']"
                        progress-class="bg-red-500"
                    />

                    <x-mailcoach::statistic
                        :href="route('mailcoach.campaigns.outbox', $campaign) . '?filter[type]=bounced'"
                        :stat="$stats['bounce_rate'] / 100"
                        :label="__mc('Bounce Rate')"
                        suffix="%"
                        :progress="$stats['bounce_rate'] / 100"
                        :progress-tooltip="$stats['bounce_count']"
                        progress-class="bg-red-500"
                    />
                </div>
            </x-mailcoach::card>
        @endforeach
    </div>
    @if ($campaign->isSplitTested() && ! $campaign->hasSplitTestWinner())
        @if($campaign->isSplitTestStarted())
            <x-mailcoach::help full class="shadow">
                {!! __mc('Winner will be decided at <strong>:date</strong>.', [
                    'date' => $campaign->split_test_started_at->addMinutes($campaign->split_test_wait_time_in_minutes)->toMailcoachFormat(),
                ]) !!}
            </x-mailcoach::help>
        @else
            <x-mailcoach::help full class="shadow">
                {{ __mc('Winner will be decided when both splits have finished sending.') }}
            </x-mailcoach::help>
        @endif
    @endif
@endif
