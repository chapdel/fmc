<?php /** @var \Spatie\Mailcoach\Domain\Automation\Models\AutomationMail $mail */ ?>
<x-mailcoach::card>
    <div class="grid gap-2">
        @if($mail->isReady())
            @if (! $mail->contentItem->htmlContainsUnsubscribeUrlPlaceHolder() || $mail->contentItem->sizeInKb() > 102)
                <x-mailcoach::warning>
                    @if ($mail->isReady() && $mail->contentItem->sendsCount() > 0)
                        <div class="mb-2 flex items-center gap-2">
                            <i class="fas fa-sync fa-spin text-orange-500"></i>
                            <p>
                                {{ __mc('Automation mail') }}
                                <a class="font-semibold" target="_blank"
                                   href="{{ $mail->webviewUrl() }}">{{ $mail->name }}</a>

                                {{ __mc('has been sent to :sendsCount :subscriber', [
                                    'sendsCount' => $mail->contentItem->sendsCount(),
                                    'subscriber' => __mc_choice('subscriber|subscribers', $mail->contentItem->sendsCount())
                                ]) }}.
                            </p>
                        </div>
                    @endif
                    <p>{!! __mc('Automation mail <strong>:automationMail</strong> can be sent, but you might want to check your content.', ['automationMail' => $mail->name]) !!}</p>
                </x-mailcoach::warning>
            @else
                @if ($mail->isReady() && $mail->contentItem->sendsCount() > 0)
                    <x-mailcoach::success>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-sync fa-spin text-green-500"></i>
                            <p>
                                {{ __mc('Automation mail') }}
                                <a class="font-semibold" target="_blank"
                                   href="{{ $mail->webviewUrl() }}">{{ $mail->name }}</a>

                                {{ __mc('has been sent to :sendsCount :subscriber', [
                                    'sendsCount' => $mail->contentItem->sendsCount(),
                                    'subscriber' => __mc_choice('subscriber|subscribers', $mail->contentItem->sendsCount())
                                ]) }}.
                            </p>
                        </div>
                    </x-mailcoach::success>
                @elseif (! $mail->contentItem->hasValidHtml())
                    <x-mailcoach::error>
                        {!! __mc('Your campaign HTML is invalid according to <a href=":url" target="_blank">the guidelines</a>, please make sure it displays correctly in the email clients you need.', ['url' => 'https://www.caniemail.com/']) !!}
                    </x-mailcoach::error>
                @else
                    <x-mailcoach::success>
                        {!! __mc('Automation mail <strong>:automationMail</strong> is ready to be sent.', ['automationMail' => $mail->name]) !!}
                    </x-mailcoach::success>
                @endif
            @endif
        @else
            <x-mailcoach::error>
                {{ __mc('You need to check some settings before you can deliver this mail.') }}
            </x-mailcoach::error>
        @endif
    </div>

    <dl class="mt-8 dl">
        <dt>
            <x-mailcoach::health-label reverse :test="$mail->contentItem->subject" :label="__mc('Subject')"/>
        </dt>

        <dd>
            {{ $mail->contentItem->subject ?? __mc('Subject is empty') }}
        </dd>

        <dt>
            @if($mail->contentItem->html && $mail->contentItem->hasValidHtml())
                <x-mailcoach::health-label reverse
                                           :test="$mail->contentItem->htmlContainsUnsubscribeUrlPlaceHolder() && $mail->contentItem->sizeInKb() < 102"
                                           warning="true"
                                           :label="__mc('Content')"/>
            @else
                <x-mailcoach::health-label reverse :test="false" :label="__mc('Content')"/>
            @endif
        </dt>

        <dd class="grid gap-4 max-w-full overflow-scroll">
            @if($mail->contentItem->html && $mail->contentItem->hasValidHtml())
                @if ($mail->contentItem->htmlContainsUnsubscribeUrlPlaceHolder() && $mail->contentItem->sizeInKb() < 102)
                    <p class="markup-code">
                        {{ __mc('No problems detected!') }}
                    </p>
                @else
                    @if (! $mail->contentItem->htmlContainsUnsubscribeUrlPlaceHolder())
                        <p class="markup-code">
                            {{ __mc("Without a way to unsubscribe, there's a high chance that your subscribers will complain.") }}
                            {!! __mc('Consider adding the <code>{{ unsubscribeUrl }}</code> placeholder.') !!}
                        </p>
                    @endif
                    @if ($mail->contentItem->sizeInKb() >= 102)
                        <p class="markup-code">
                            {{ __mc("Your email's content size is larger than 102kb (:size). This could cause Gmail to clip your mail.", ['size' => "{$mail->contentItem->sizeInKb()}kb"]) }}
                        </p>
                    @endif
                @endif
            @else
                @if(empty($mail->contentItem->html))
                    {{ __mc('Content is missing') }}
                @else
                    <p>{{ __mc('HTML is invalid') }}</p>
                    <p>{!! $mail->contentItem->htmlError() !!}</p>
                @endif
            @endif

            @if($mail->contentItem->html && $mail->contentItem->hasValidHtml())
                <div>
                    <x-mailcoach::button-secondary x-on:click="$dispatch('open-modal', { id: 'preview' })"
                                                   :label="__mc('Preview')"/>
                    <x-mailcoach::button-secondary x-on:click="$dispatch('open-modal', { id: 'send-test' })"
                                                   :label="__mc('Send Test')"/>
                </div>

                <x-mailcoach::preview-modal :title="__mc('Preview') . ' - ' . $mail->contentItem->subject" :html="$mail->contentItem->html"/>

                <x-mailcoach::modal name="send-test" :dismissable="true">
                    <livewire:mailcoach::send-test :model="$mail->contentItem"/>
                </x-mailcoach::modal>
            @endif
        </dd>

        <dt>
            <span class="inline-flex gap-2 items-center md:flex-row-reverse">
                <x-mailcoach::rounded-icon type="neutral" icon="fas fa-link"/>
                <span>
                    {{ __mc('Links') }}
                </span>
            </span>
        </dt>

        <dd>
            @php($tags = [])
            @php($links = $mail->htmlLinks())
            @if (count($links))
                <p class="markup-code">
                    {{ __mc("The following links were found in your mail, make sure they are valid.") }}
                </p>
                <ul class="grid gap-2">
                    @foreach ($links as $link)
                        <li>
                            <livewire:mailcoach::link-check lazy :url="$link" wire:key="{{ $link }}"/>
                            @php($tags[] = \Spatie\Mailcoach\Domain\Content\Support\LinkHasher::hash($mail, $link))
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="markup-code">
                    {{ __mc("No links were found in your mail.") }}
                </p>
            @endif
        </dd>

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
                {{ __mc("The following tags will be added to subscribers when they open or click the mail:") }}
            </p>
            <ul class="flex flex-wrap space-x-2">
                @if ($mail->contentItem->add_subscriber_tags)
                    <li class="tag-neutral">{{ "automation-mail-{$mail->uuid}-opened" }}</li>
                    <li class="tag-neutral">{{ "automation-mail-{$mail->uuid}-clicked" }}</li>
                @endif
                @if ($mail->contentItem->add_subscriber_link_tags)
                    @foreach ($tags as $tag)
                        <li class="tag-neutral">{{ $tag }}</li>
                    @endforeach
                @endif
            </ul>
        </dd>
    </dl>
</x-mailcoach::card>
