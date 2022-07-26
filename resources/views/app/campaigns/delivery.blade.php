<x-mailcoach::card wire:init="checkLinks">
    @if ($campaign->isEditable())
        <div class="grid gap-2">
            @if($campaign->isReady())
                @if($campaign->scheduled_at)
                    <x-mailcoach::warning>
                        {{ __('mailcoach - Scheduled for delivery at :scheduledAt', ['scheduledAt' => $campaign->scheduled_at->toMailcoachFormat()]) }}
                    </x-mailcoach::warning>
                @endif

                @if (! $campaign->htmlContainsUnsubscribeUrlPlaceHolder() || $campaign->sizeInKb() > 102)
                    <x-mailcoach::warning>
                        {!! __('mailcoach - Campaign <strong>:campaign</strong> can be sent, but you might want to check your content.', ['campaign' => $campaign->name]) !!}
                    </x-mailcoach::warning>
                @else
                    <x-mailcoach::success>
                        {!! __('mailcoach - Campaign <strong>:campaign</strong> is ready to be sent.', ['campaign' => $campaign->name]) !!}
                    </x-mailcoach::success>
                @endif
            @else
                <x-mailcoach::error>
                    {{ __('mailcoach - You need to check some settings before you can deliver this campaign.') }}
                </x-mailcoach::error>
            @endif
        </div>
    @endif

    <dl
        class="mt-8 dl"
    >
        @if ($campaign->emailList)
            <dt>
                <x-mailcoach::health-label :test="true" :label="__('mailcoach - From')"/>
            </dt>

            <dd>
                <span>
                    {{ $campaign->emailList->default_from_email }} {{ $campaign->emailList->default_from_name ? "({$campaign->emailList->default_from_name})" : '' }}
                    <a href="{{ route('mailcoach.emailLists.general-settings', $campaign->emailList) }}" class="link">{{ strtolower(__('mailcoach - Edit')) }}</a>
                </span>
            </dd>

            @if ($campaign->emailList->default_reply_to_email)
                <dt>
                    <x-mailcoach::health-label :test="true" :label="__('mailcoach - Reply-to')"/>
                </dt>

                <dd>
                    <span>
                        {{ $campaign->emailList->default_reply_to_email }} {{ $campaign->emailList->default_reply_to_name ? "({$campaign->emailList->default_reply_to_name})" : '' }}
                        <a href="{{ route('mailcoach.emailLists.general-settings', $campaign->emailList) }}" class="link">{{ strtolower(__('mailcoach - Edit')) }}</a>
                    </span>
                </dd>
            @endif

            <dt>
                <x-mailcoach::health-label :test="$campaign->segmentSubscriberCount()" :label="__('mailcoach - To')"/>
            </dt>

            <dd>
                <div>
                    @if($campaign->emailListSubscriberCount())
                        {{ $campaign->emailList->name }}
                        @if($campaign->usesSegment())
                            ({{ $campaign->getSegment()->description() }})
                        @endif
                        <span class="ml-2 tag-neutral text-xs">
                            {{ $campaign->segmentSubscriberCount() }}
                            <span class="ml-1 font-normal">
                                {{ trans_choice('mailcoach - subscriber|subscribers', $campaign->segmentSubscriberCount()) }}
                            </span>
                        </span>
                    @elseif($campaign->emailList)
                        {{ __('mailcoach - Selected list has no subscribers') }}
                    @else
                        {{ __('mailcoach - No list selected') }}
                    @endif
                    <a href="{{ route('mailcoach.campaigns.settings', $campaign) }}" class="link">{{ strtolower(__('mailcoach - Edit')) }}</a>
                </div>
            </dd>
        @endif

        <dt>
            <x-mailcoach::health-label :test="$campaign->subject" :label="__('mailcoach - Subject')"/>
        </dt>

        <dd>
            <span>
                {{ $campaign->subject ?? __('mailcoach - Subject is empty') }}
                <a href="{{ route('mailcoach.campaigns.settings', $campaign) }}" class="link">{{ strtolower(__('mailcoach - Edit')) }}</a>
            </span>
        </dd>

        @if ($campaign->emailList)
            <dt>
                <x-mailcoach::health-label warning :test="$campaign->getMailerKey() && $campaign->getMailerKey() !== 'log'" :label="__('mailcoach - Mailer')"/>
            </dt>
            <dd>
                <div>
                    {{ $campaign->getMailer()?->name ?? $campaign->emailList->campaign_mailer }} <a href="{{ route('mailcoach.emailLists.mailers', $campaign->emailList) }}" class="link">{{ strtolower(__('mailcoach - Edit')) }}</a>
                </div>
            </dd>
        @endif

        <dt>
            @if($campaign->html && $campaign->hasValidHtml())
                <x-mailcoach::health-label
                    :test="$campaign->htmlContainsUnsubscribeUrlPlaceHolder() && $campaign->sizeInKb() < 102"
                    warning="true"
                    :label="__('mailcoach - Content')"/>
            @else
                <x-mailcoach::health-label :test="false" :label="__('mailcoach - Content')"/>
            @endif
        </dt>


        <dd class="grid gap-4">
            @if($campaign->html && $campaign->hasValidHtml())
                @if ($campaign->htmlContainsUnsubscribeUrlPlaceHolder() && $campaign->sizeInKb() < 102)
                    <p class="markup-code">
                        {{ __('mailcoach - No problems detected!') }}
                    </p>
                @else
                    @if (! $campaign->htmlContainsUnsubscribeUrlPlaceHolder())
                        <p class="markup-code">
                            {{ __("mailcoach - Without a way to unsubscribe, there's a high chance that your subscribers will complain.") }}
                            {!! __('mailcoach - Consider adding the <code>::unsubscribeUrl::</code> placeholder.') !!}
                        </p>
                    @endif
                    @if ($campaign->sizeInKb() >= 102)
                        <p class="markup-code">
                            {{ __("mailcoach - Your email's content size is larger than 102kb (:size). This could cause Gmail to clip your campaign.", ['size' => "{$campaign->sizeInKb()}kb"]) }}
                        </p>
                    @endif
                @endif
            @else
                @if(empty($campaign->html))
                    {{ __('mailcoach - Content is missing') }}
                @else
                    {{ __('mailcoach - HTML is invalid') }}
                @endif
            @endif

            @if($campaign->html && $campaign->hasValidHtml())
                <div class="buttons gap-4">
                    <x-mailcoach::button-secondary x-on:click="$store.modals.open('preview')" :label="__('mailcoach - Preview')"/>
                    <x-mailcoach::button-secondary x-on:click="$store.modals.open('send-test')" :label="__('mailcoach - Send Test')"/>
                </div>

                <x-mailcoach::preview-modal :title="__('mailcoach - Preview') . ' - ' . $campaign->subject" :html="$campaign->html" />

                <x-mailcoach::modal :title="__('mailcoach - Send Test')" name="send-test" :dismissable="true">
                    <livewire:mailcoach::send-test :model="$campaign" />
                </x-mailcoach::modal>
            @endif
        </dd>

        <dt>
            <span class="inline-flex gap-2 items-center md:flex-row-reverse">
                <x-mailcoach::rounded-icon type="neutral" icon="fas fa-link"/>
                <span>
                    {{ __('mailcoach - Links') }}
                </span>
            </span>
        </dt>

        <dd>
            @php($tags = [])
            @if (count($links))
                <p class="markup-code">
                    {{ __("mailcoach - The following links were found in your campaign, make sure they are valid.") }}
                </p>
                <ul class="grid gap-2">
                    @foreach ($links as $url => $status)
                        <li class="flex items-center gap-x-1">
                            <a target="_blank" class="link" href="{{ $url }}">{{ $url }}</a>
                            @if (!is_null($status))
                                <x-mailcoach::health-label warning :test="$status" />
                            @endif
                            @php($tags[] = \Spatie\Mailcoach\Domain\Shared\Support\LinkHasher::hash($campaign, $url))
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="markup-code">
                    {{ __("mailcoach - No links were found in your campaign.") }}
                </p>
            @endif
        </dd>

        @php([$openTracking, $clickTracking] = $campaign->tracking())
        @if ($openTracking || $clickTracking || (is_null($openTracking) && is_null($clickTracking)))
            <dt>
                <span class="inline-flex gap-2 items-center md:flex-row-reverse">
                    <x-mailcoach::rounded-icon type="neutral" icon="fas fa-tag"/>
                    <span>
                        {{ __('mailcoach - Tags') }}
                    </span>
                </span>
            </dt>

            <dd>
                <p class="markup-code">
                    {{ __("mailcoach - The following tags will be added to subscribers when they open or click the campaign:") }}
                </p>
                @if (is_null($openTracking) && is_null($clickTracking))
                    <p class="markup-code">
                        {!! __('mailcoach - Open & Click tracking are managed by your email provider, this campaign uses the <strong>:mailer</strong> mailer.', ['mailer' => $campaign->emailList->campaign_mailer]) !!}
                    </p>
                @endif
                <ul class="flex flex-wrap space-x-2">
                    <li class="tag">{{ "campaign-{$campaign->id}-opened" }}</li>
                    <li class="tag">{{ "campaign-{$campaign->id}-clicked" }}</li>
                    @foreach ($tags as $tag)
                        <li class="tag">{{ $tag }}</li>
                    @endforeach
                </ul>
            </dd>
        @endif

        @if ($campaign->isReady())
            <dt>
                <span class="inline-flex gap-2 items-center md:flex-row-reverse">
                    <x-mailcoach::rounded-icon :type="$campaign->scheduled_at ? 'warning' : 'neutral'"
                                               icon="far fa-clock"/>
                    <span>
                        {{ __('mailcoach - Timing') }}
                    </span>
                </span>
            </dt>

            <dd x-init="schedule = '{{ $campaign->scheduled_at || $errors->first('scheduled_at') ? 'future' : 'now' }}'" x-data="{ schedule: '' }" x-cloak>
                @if($campaign->scheduled_at)
                    <div>
                        <p class="mb-3">
                            {{ __('mailcoach - This campaign is scheduled to be sent at') }}

                            <strong>{{ $campaign->scheduled_at->toMailcoachFormat() }}</strong>.
                        </p>
                        <button type="submit" wire:click.prevent="unschedule" class="button-secondary">
                            {{ __('mailcoach - Unschedule') }}
                        </button>
                    </div>
                @elseif ($campaign->isEditable())
                    <div class="radio-group">
                        <x-mailcoach::radio-field
                            name="schedule"
                            option-value="now"
                            :label="__('mailcoach - Send immediately')"
                            x-model="schedule"
                        />
                        <x-mailcoach::radio-field
                            name="schedule"
                            option-value="future"
                            :label="__('mailcoach - Schedule for delivery in the future')"
                            x-model="schedule"
                        />
                    </div>

                    <form
                        method="POST"
                        wire:submit.prevent="schedule"
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
                                {{ __('mailcoach - Schedule delivery') }}
                            </button>
                        </div>
                        <p class="mt-2 text-xs text-gray-400">
                            {{ __('mailcoach - All times in :timezone', ['timezone' => config('app.timezone')]) }}
                        </p>
                    </form>
                @elseif (! $campaign->sent_to_number_of_subscribers)
                    <div class="flex alert alert-info">
                        <div class="mr-2">
                            <i class="fas fa-sync fa-spin text-blue-500"></i>
                        </div>
                        <div>
                            {{ __('mailcoach - Campaign') }}
                            <a target="_blank" href="{{ $campaign->webviewUrl() }}">{{ $campaign->name }}</a>

                            {{ __('mailcoach - is preparing to send to') }}

                            @if($campaign->emailList)
                                <a href="{{ route('mailcoach.emailLists.subscribers', $campaign->emailList) }}">{{ $campaign->emailList->name }}</a>
                            @else
                                &lt;{{ __('mailcoach - deleted list') }}&gt;
                            @endif
                        </div>
                    </div>
                @elseif ($campaign->isCancelled())
                    <div class="flex alert alert-info">
                        <div class="mr-2">
                            <i class="fas fa-ban text-red-500"></i>
                        </div>
                        <div class="flex justify-between items-center w-full">
                            <p>
                                <span class="inline-block">{{ __('mailcoach - Campaign') }}</span>
                                <a class="inline-block" target="_blank"
                                   href="{{ $campaign->webviewUrl() }}">{{ $campaign->name }}</a>

                                {{ __('mailcoach - sending is cancelled.', [
                                    'sendsCount' => $campaign->sendsCount(),
                                    'sentToNumberOfSubscribers' => $campaign->sent_to_number_of_subscribers,
                                    'subscriber' => trans_choice('mailcoach - subscriber|subscribers', $campaign->sent_to_number_of_subscribers)
                                ]) }}

                                {{ __('mailcoach - It was sent to :sendsCount/:sentToNumberOfSubscribers :subscriber of', [
                                    'sendsCount' => $campaign->sendsCount(),
                                    'sentToNumberOfSubscribers' => $campaign->sent_to_number_of_subscribers,
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
                    </div>
                @else
                    <div class="flex alert alert-info">
                        <div class="mr-2">
                            <i class="fas fa-sync fa-spin text-blue-500"></i>
                        </div>
                        <div class="flex justify-between items-center w-full">
                            <p>
                                <span class="inline-block">{{ __('mailcoach - Campaign') }}</span>
                                <a class="inline-block" target="_blank"
                                   href="{{ $campaign->webviewUrl() }}">{{ $campaign->name }}</a>

                                {{ __('mailcoach - is sending to :sendsCount/:sentToNumberOfSubscribers :subscriber of', [
                                    'sendsCount' => $campaign->sendsCount(),
                                    'sentToNumberOfSubscribers' => $campaign->sent_to_number_of_subscribers,
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
                    </div>
                @endif

                @if ($campaign->isEditable())
                    <div
                        class="buttons"
                        x-show="schedule === 'now'"
                    >
                        <x-mailcoach::button x-on:click="$store.modals.open('send-campaign')" :label="__('mailcoach - Send now')"/>
                    </div>
                    <x-mailcoach::modal name="send-campaign" :dismissable="true" x-data>
                        <div class="grid gap-8 p-6">
                            <p class="text-lg">
                                {{ __('mailcoach - Are you sure you want to send this campaign to') }}
                                <strong class="font-semibold">
                                    {{ number_format($campaign->segmentSubscriberCount()) }}
                                    {{ $campaign->segmentSubscriberCount() === 1 ? __('mailcoach - subscriber') : __('mailcoach - subscribers') }}
                                </strong>?
                            </p>

                            <x-mailcoach::button
                                x-on:click.prevent="Livewire.emit('send-campaign')"
                                class="button button-red"
                                :label="__('mailcoach - Yes, send now!')"
                            />
                        </div>
                    </x-mailcoach::modal>
                @endif
            </dd>
        @endif
    </dl>
</x-mailcoach::card>
