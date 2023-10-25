<?php /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign $campaign */ ?>
<div class="grid grid-cols-1 gap-6" wire:init="loadData">
    @if ($campaign->isEditable())
        <div class="grid gap-2">
            @if($campaign->isReady())
                @if ($campaign->contentItems->reject->htmlContainsUnsubscribeUrlPlaceHolder()->count() || $campaign->contentItems->filter(fn ($contentItem) => $contentItem->sizeInKb() > 102)->count())
                    <x-mailcoach::warning class="shadow" full>
                        {!! __mc('Campaign <strong>:campaign</strong> can be sent, but you might want to check your content.', ['campaign' => $campaign->name]) !!}

                        @if($campaign->scheduled_at)
                            <div class="mt-4 flex gap-2 items-center">
                                <x-mailcoach::rounded-icon type="warning" icon="far fa-clock"/>
                                <span class="font-semibold">
                                    {{ __mc('Scheduled for delivery at :scheduledAt', ['scheduledAt' => $campaign->scheduled_at->toMailcoachFormat()]) }}.
                                </span>
                            </div>
                        @endif
                    </x-mailcoach::warning>
                @else
                    <x-mailcoach::success class="shadow" full>
                        {!! __mc('Campaign <strong>:campaign</strong> is ready to be sent.', ['campaign' => $campaign->name]) !!}

                        @if($campaign->scheduled_at)
                            <div class="mt-4 flex gap-2 items-center">
                                <x-mailcoach::rounded-icon type="success" icon="far fa-clock"/>
                                <span class="font-semibold">
                                    {{ __mc('Scheduled for delivery at :scheduledAt', ['scheduledAt' => $campaign->scheduled_at->toMailcoachFormat()]) }}.
                                </span>
                            </div>
                        @endif
                    </x-mailcoach::success>
                @endif
            @else
                <x-mailcoach::error class="shadow" full>
                    {{ __mc('You need to check some settings before you can deliver this campaign.') }}
                </x-mailcoach::error>
            @endif
        </div>
    @endif
    <x-mailcoach::line-title>
        {{ __mc('Settings') }}
    </x-mailcoach::line-title>
    <x-mailcoach::card>
        <dl class="dl max-w-full">
        @if ($campaign->emailList)
            <dt>
                <x-mailcoach::health-label reverse :test="true" :label="__mc('From')"/>
            </dt>

            <dd>
                <span>
                    {{ $fromEmail }} @if ($fromName)
                        ({{ $fromName }})
                    @endif
                    <a href="{{ route('mailcoach.emailLists.general-settings', $campaign->emailList) }}"
                       class="link"><i class="far fa-edit"></i></a>
                </span>
            </dd>

            @if ($replyToEmail)
                <dt>
                    <x-mailcoach::health-label reverse :test="true" :label="__mc('Reply-to')"/>
                </dt>

                <dd>
                    <span>
                        {{ $replyToEmail }} @if ($replyToName)
                            ({{ $replyToName }})
                        @endif
                        <a href="{{ route('mailcoach.emailLists.general-settings', $campaign->emailList) }}"
                           class="link"><i class="far fa-edit"></i></a>
                    </span>
                </dd>
            @endif

            <dt>
                @if (! is_null($subscribersCount))
                    <x-mailcoach::health-label reverse :test="$subscribersCount" :label="__mc('To')"/>
                @else
                    ...
                @endif
            </dt>

            <dd>
                <div>
                    @if($subscribersCount = $campaign->emailListSubscriberCount())
                        {{ $campaign->emailList->name }}
                        @if($campaign->usesSegment())
                            ({{ $campaign->getSegment()->description() }})
                        @endif
                        <span class="ml-2 tag-neutral text-xs">
                            {{ $subscribersCount ?? '...' }}
                            @if (!is_null($subscribersCount))
                                <span class="ml-1 font-normal">
                                {{ __mc_choice('subscriber|subscribers', $subscribersCount) }}
                            </span>
                            @endif
                        </span>
                    @elseif($campaign->emailList)
                        {{ __mc('Selected list has no subscribers') }}
                    @else
                        {{ __mc('No list selected') }}
                    @endif
                    <a href="{{ route('mailcoach.campaigns.settings', $campaign) }}"
                       class="link"><i class="far fa-edit"></i></a>
                </div>
            </dd>
        @else
            <dt>
                <x-mailcoach::health-label reverse :test="false" :label="__mc('Email list')"/>
            </dt>

            <dd>
                <span>
                    {{ __mc('No email list') }}
                    <a href="{{ route('mailcoach.campaigns.settings', $campaign) }}"
                       class="link"><i class="far fa-edit"></i></a>
                </span>
            </dd>
        @endif

        @if ($campaign->emailList)
            <dt>
                <x-mailcoach::health-label
                    reverse
                    warning
                    :test="$campaign->getMailerKey() && $campaign->getMailerKey() !== 'log'"
                    :label="__mc('Mailer')"
                />
            </dt>
            <dd>
                <div>
                    {{ $campaign->getMailer()?->name ?? $campaign->emailList->campaign_mailer }}
                    <a
                        href="{{ route('mailcoach.emailLists.mailers', $campaign->emailList) }}"
                        class="link"
                    ><i class="far fa-edit"></i></a>
                </div>
            </dd>
        @endif

        </dl>
    </x-mailcoach::card>

    <x-mailcoach::line-title>
        {{ __mc('Content') }}
    </x-mailcoach::line-title>
    <div class="grid grid-cols-{{ $campaign->isSplitTested() ? '2' : '1' }} gap-6">
        @foreach ($campaign->contentItems as $index => $contentItem)
            <x-mailcoach::card class="relative {{ $campaign->isSplitTested() ? 'pt-20' : '' }}">
                @if ($campaign->isSplitTested())
                    <div class="absolute flex justify-center w-full top-0 left-0 right-0 pt-4">
                        <div class="mx-auto w-8 h-8 rounded-full inline-flex items-center justify-center text-sm leading-none font-semibold counter-automation">
                            {{ $index + 1 }}
                        </div>
                    </div>
                @endif
                <dl class="dl max-w-full mb-auto">
                    <dt>
                        <x-mailcoach::health-label reverse :test="$contentItem->subject" :label="__mc('Subject')"/>
                    </dt>
                    <dd>
                        <span>
                            {{ $contentItem->subject ?: __mc('Subject is empty') }}
                            <a href="{{ route('mailcoach.campaigns.settings', $campaign) }}" class="link ml-1">
                                <i class="far fa-edit"></i>
                            </a>
                        </span>
                    </dd>

                    <dt>
                        @if($contentItem->html && $contentItem->hasValidHtml())
                            <x-mailcoach::health-label reverse
                                                       :test="$contentItem->htmlContainsUnsubscribeUrlPlaceHolder() && $contentItem->sizeInKb() < 102"
                                                       warning="true"
                                                       :label="__mc('Content')"/>
                        @else
                            <x-mailcoach::health-label reverse :test="false" :label="__mc('Content')"/>
                        @endif
                    </dt>


                    <dd class="grid gap-4 max-w-full overflow-scroll">
                        @if($contentItem->html)
                            @if (! $contentItem->hasValidHtml())
                                <p>{{ __mc('HTML is invalid') }}</p>
                                <p>{!! $contentItem->htmlError() !!}</p>
                            @endif
                            @if (! $contentItem->htmlContainsUnsubscribeUrlPlaceHolder())
                                <p class="markup-code">
                                    {{ __mc("Without a way to unsubscribe, there's a high chance that your subscribers will complain.") }}
                                    {!! __mc('Consider adding the <code>&#123;&#123; unsubscribeUrl &#125;&#125;</code> placeholder.') !!}
                                </p>
                            @endif
                            @if ($contentItem->sizeInKb() >= 102)
                                <p class="markup-code">
                                    {{ __mc("Your email's content size is larger than 102kb (:size). This could cause Gmail to clip your campaign.", ['size' => "{$campaign->sizeInKb()}kb"]) }}
                                </p>
                            @endif

                            @if ($contentItem->hasValidHtml() && $contentItem->htmlContainsUnsubscribeUrlPlaceHolder() && $contentItem->sizeInKb() < 102)
                                <p class="markup-code">
                                    {{ __mc('No problems detected!') }}
                                </p>
                            @endif
                        @else
                            {{ __mc('Content is missing') }}
                        @endif

                        @if($contentItem->html)
                            <div>
                                <x-mailcoach::button-secondary
                                    x-on:click="$dispatch('open-modal', { id: 'preview-{{ $contentItem->uuid }}' })"
                                    :label="__mc('Preview')"
                                />
                                @if ($campaign->getMailerKey())
                                    <x-mailcoach::button-secondary
                                        x-on:click="$dispatch('open-modal', { id: 'send-test-{{ $contentItem->uuid }}' })"
                                        :label="__mc('Send Test')"
                                    />
                                @endif
                            </div>

                            <x-mailcoach::preview-modal
                                :id="'preview-'. $contentItem->uuid"
                                :title="__mc('Preview') . ' - ' . $contentItem->subject"
                                :html="$contentItem->html"
                            />

                            <x-mailcoach::modal :title="__mc('Send Test')" name="send-test-{{ $contentItem->uuid }}" :dismissable="true">
                                <livewire:mailcoach::send-test :model="$contentItem"/>
                            </x-mailcoach::modal>
                        @endif
                    </dd>

                    <dt>
                    <span class="inline-flex gap-2 items-center md:flex-row-reverse">
                        <span>
                            {{ __mc('Links') }}
                        </span>
                        <x-mailcoach::rounded-icon type="neutral" icon="fas fa-link"/>
                    </span>
                    </dt>

                    <dd>
                        @php($tags = [])
                        @php($links = $contentItem->htmlLinks())
                        @if (count($links))
                            <p class="markup-code">
                                {{ __mc("The following links were found in your campaign, make sure they are valid.") }}
                            </p>
                            <div x-data="{ collapsed: @js(count($links) > 5) }">
                                <div class="flex gap-x-2 mb-2" x-cloak>
                                    <span>{{ count($links) }} {{ __mc('links') }}</span>
                                    <button class="button-secondary flex items-center p-0 !m-0" type="button" x-show="collapsed" x-on:click="collapsed = !collapsed">
                                        {{ __mc('Show') }}
                                    </button>
                                    <button class="button-secondary flex items-center p-0 !m-0" type="button" x-show="!collapsed" x-on:click="collapsed = !collapsed">
                                       {{ __mc('Hide') }}
                                    </button>
                                </div>
                                <ul class="grid gap-2 ml-1" x-show="!collapsed" x-collapse>
                                    @foreach ($links as $index => $link)
                                        @php($key = $contentItem->id . $link)
                                        <li>
                                            <livewire:mailcoach::link-check lazy :url="$link" wire:key="{{ $key }}"/>
                                            @php($tags[] = \Spatie\Mailcoach\Domain\Content\Support\LinkHasher::hash($campaign, $link))
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <p class="markup-code">
                                {{ __mc("No links were found in your campaign.") }}
                            </p>
                        @endif
                    </dd>

                    @php([$openTracking, $clickTracking] = $campaign->tracking())
                    @if ($openTracking || $clickTracking || (is_null($openTracking) && is_null($clickTracking)))
                        @if ($contentItem->add_subscriber_tags || $contentItem->add_subscriber_link_tags)
                            <dt>
                        <span class="inline-flex gap-2 items-center md:flex-row-reverse">
                            <x-mailcoach::rounded-icon type="neutral" icon="fas fa-tag"/>
                            <span>
                                {{ __mc('Tags') }}
                            </span>
                        </span>
                            </dt>

                            <dd>
                                <p class="markup-code">
                                    {{ __mc("The following tags will be added to subscribers when they open or click the campaign:") }}
                                </p>
                                @if (is_null($openTracking) && is_null($clickTracking))
                                    <p class="markup-code">
                                        {!! __mc('Open & Click tracking are managed by your email provider, this campaign uses the <strong>:mailer</strong> mailer.', ['mailer' => $campaign->getMailerKey()]) !!}
                                    </p>
                                @endif
                                <ul class="flex flex-wrap space-x-2">
                                    @if ($contentItem->add_subscriber_tags)
                                        <li class="tag-neutral">{{ "campaign-{$campaign->uuid}-opened" }}</li>
                                        <li class="tag-neutral">{{ "campaign-{$campaign->uuid}-clicked" }}</li>
                                    @endif
                                    @if ($contentItem->add_subscriber_link_tags)
                                        @foreach ($tags as $tag)
                                            <li class="tag-neutral">{{ $tag }}</li>
                                        @endforeach
                                    @endif
                                </ul>
                            </dd>
                        @endif
                    @endif
                </dl>
            </x-mailcoach::card>
        @endforeach
    </div>

    @if ($campaign->isSplitTested() && $subscribersCount)
        <x-mailcoach::line-title>
            {{ __mc('Split test settings') }}
        </x-mailcoach::line-title>
        <x-mailcoach::card>
            <div class="text-lg" x-data="{
                split_size_percentage: @entangle('split_test_split_size_percentage'),
                split_count: {{ $campaign->contentItems->count() }},
                subscriber_count: {{ $campaign->segmentSubscriberCount() }},

                get subscribers_in_test() {
                    return Math.max(this.split_count, Math.floor(this.subscriber_count / 100 * this.split_size_percentage));
                },

                get subscribers_per_split() {
                    return Math.max(1, Math.floor(this.subscribers_in_test / this.split_count));
                }
            }">
                <div class="flex max-w-2xl mx-auto mb-8">
                    <div class="w-full flex flex-col gap-y-2 items-center text-center">
                        <span class="mb-2 w-10 h-10 relative -top-[4px] rounded-full inline-flex items-center justify-center text-sm leading-none font-semibold counter-automation">
                            1
                        </span>
                        <p>{{ __mc('Send to') }} <span class="font-semibold" x-text="split_size_percentage + '%'"></span> (<span x-text="subscribers_in_test + ' {{ __mc('emails') }}'"></span>) {{ __mc('of subscribers') }}.</p>
                        <div><input class="h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700" type="range" min="1" step="1" max="50" x-model="split_size_percentage"></div>
                    </div>
                    <div class="w-full flex flex-col gap-y-2 items-center text-center">
                        <span class="mb-2 w-10 h-10 relative -top-[4px] rounded-full inline-flex items-center justify-center text-sm leading-none font-semibold counter-automation">
                            2
                        </span>
                        <div>
                            <div class="flex items-center gap-x-2">
                                {{__mc('Wait for') }}
                                <x-mailcoach::text-field
                                    :required="true"
                                    name="split_length"
                                    wire:model.live="split_length"
                                    type="number"
                                    min="1"
                                    input-class="w-12 min-h-0 h-8"
                                />
                                @if ($split_length > 1)
                                    {{ __mc('hours') }}
                                @else
                                    {{ __mc('hour') }}
                                @endif
                            </div>
                        </div>
                        <div>
                            {{ __mc('until deciding a winner') }}
                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <p>{{ __mc('Each split will receive') }} <span class="font-semibold" x-text="subscribers_per_split + (subscribers_per_split === 1 ? ' {{  __mc('email') }}' : ' {{  __mc('emails') }}')"></span>.</p>
                    <p>{{ __mc('The winner will receive the remaining') }} <span class="font-semibold" x-text="(Math.max(0, subscriber_count - (subscribers_per_split * split_count))) + ' {{ __mc('emails') }}'"></span></p>
                </div>
            </div>

            <div class="flex justify-center">
                <x-mailcoach::button wire:click.prevent="saveSplitTestSettings">{{ __mc('Save settings') }}</x-mailcoach::button>
            </div>
        </x-mailcoach::card>
    @endif

    <x-mailcoach::line-title>
        {{ __mc('Send campaign') }}
    </x-mailcoach::line-title>
    <x-mailcoach::card>
        @if (count($validateErrors = $campaign->validateRequirements()))
            @foreach ($validateErrors as $error)
                <x-mailcoach::error full>{!! $error !!}</x-mailcoach::error>
            @endforeach
        @endif
        <div>
            @if ($campaign->isReady())
                <div class="w-full flex flex-col items-center" x-init="schedule = '{{ $campaign->scheduled_at || $errors->first('scheduled_at') ? 'future' : 'now' }}'"
                    x-data="{ schedule: '' }" x-cloak>
                    @if($campaign->scheduled_at)
                        <x-mailcoach::success class="w-full" full>
                            <p class="mb-3">
                                {{ __mc('This campaign is scheduled to be sent at') }}

                                <strong>{{ $campaign->scheduled_at->toMailcoachFormat() }}</strong>.
                            </p>
                        </x-mailcoach::success>
                        <x-mailcoach::button class="mt-4" type="submit" wire:click.prevent="unschedule">
                            {{ __mc('Unschedule') }}
                        </x-mailcoach::button>
                    @else
                        <div class="radio-group mb-6">
                            <x-mailcoach::radio-field
                                name="schedule"
                                option-value="now"
                                :label="__mc('Send immediately')"
                                x-model="schedule"
                            />
                            <x-mailcoach::radio-field
                                name="schedule"
                                option-value="future"
                                :label="__mc('Schedule for delivery in the future')"
                                x-model="schedule"
                            />
                        </div>

                        <form
                            method="POST"
                            wire:submit="schedule"
                            x-show="schedule === 'future'"
                        >
                            @csrf
                            <div class="flex items-end">
                                <x-mailcoach::date-time-field
                                    name="scheduled_at"
                                    :value="$scheduled_at_date"
                                    required
                                />

                                <button type="submit" class="ml-6 button">
                                    {{ __mc('Schedule delivery') }}
                                </button>
                            </div>
                            <p class="mt-2 text-xs text-gray-400">
                                {{ __mc('All times in :timezone', ['timezone' => config('mailcoach.timezone') ?? config('app.timezone')]) }}
                            </p>
                        </form>
                    @endif

                    <div x-show="schedule === 'now'">
                        <x-mailcoach::button
                            x-on:click="$dispatch('open-modal', { id: 'send-campaign' })"
                            :label="__mc('Send now')"
                        />
                    </div>
                    <x-mailcoach::modal name="send-campaign" :dismissable="true">
                        <div class="grid gap-8 p-6">
                            <p class="text-lg">
                                {{ __mc('Are you sure you want to send this campaign to') }}
                                <strong class="font-semibold">
                                    @if ($subscribersCount = $campaign->segmentSubscriberCount())
                                        {{ number_format($subscribersCount) }}
                                        {{ $subscribersCount === 1 ? __mc('subscriber') : __mc('subscribers') }}
                                    @endif
                                </strong>?
                            </p>

                            <x-mailcoach::button
                                x-on:click.prevent="$dispatch('send-campaign')"
                                class="button button-red"
                                :label="__mc('Yes, send now!')"
                            />
                        </div>
                    </x-mailcoach::modal>
                </div>
            @else
                <x-mailcoach::error class="shadow" full>
                    {{ __mc('You need to check some settings before you can deliver this campaign.') }}
                </x-mailcoach::error>
            @endif
        </div>
    </x-mailcoach::card>
</div>
