<div>
    <div class="grid gap-2">
        @if($mail->isReady())
            @if (! $mail->htmlContainsUnsubscribeUrlPlaceHolder() || $mail->sizeInKb() > 102)
                <x-mailcoach::warning>
                    {!! __('mailcoach - Automation mail <strong>:mail</strong> can be sent, but you might want to check your content.', ['mail' => $mail->name]) !!}
                </x-mailcoach::warning>
            @else
                <x-mailcoach::success>
                    {!! __('mailcoach - Automation mail <strong>:mail</strong> is ready to be sent.', ['mail' => $mail->name]) !!}
                </x-mailcoach::success>
            @endif
        @else
            <x-mailcoach::error>
                {{ __('mailcoach - You need to check some settings before you can deliver this mail.') }}
            </x-mailcoach::error>
        @endif
    </div>

    <dl class="mt-8 dl">
        <dt>
            <x-mailcoach::health-label :test="$mail->subject" :label="__('mailcoach - Subject')"/>
        </dt>

        <dd>
            {{ $mail->subject ?? __('mailcoach - Subject is empty') }}
        </dd>

        <dt>
            @if($mail->html && $mail->hasValidHtml())
                <x-mailcoach::health-label
                    :test="$mail->htmlContainsUnsubscribeUrlPlaceHolder() && $mail->sizeInKb() < 102"
                    warning="true"
                    :label="__('mailcoach - Content')"/>
            @else
                <x-mailcoach::health-label :test="false" :label="__('mailcoach - Content')"/>
            @endif
        </dt>

        <dd class="grid gap-4">
            @if($mail->html && $mail->hasValidHtml())
                @if ($mail->htmlContainsUnsubscribeUrlPlaceHolder() && $mail->sizeInKb() < 102)
                    <p class="markup-code">
                        {{ __('mailcoach - Content seems fine.') }}
                    </p>
                @else
                    @if (! $mail->htmlContainsUnsubscribeUrlPlaceHolder())
                        <p class="markup-code">
                            {{ __("mailcoach - Without a way to unsubscribe, there's a high chance that your subscribers will complain.") }}
                            {!! __('mailcoach - Consider adding the <code>::unsubscribeUrl::</code> placeholder.') !!}
                        </p>
                    @endif
                    @if ($mail->sizeInKb() >= 102)
                        <p class="markup-code">
                            {{ __("mailcoach - Your email's content size is larger than 102kb (:size). This could cause Gmail to clip your mail.", ['size' => "{$mail->sizeInKb()}kb"]) }}
                        </p>
                    @endif
                @endif
            @else
                @if(empty($mail->html))
                    {{ __('mailcoach - Content is missing') }}
                @else
                    {{ __('mailcoach - HTML is invalid') }}
                @endif
            @endif

            @if($mail->html && $mail->hasValidHtml())
                <div class="buttons gap-4">
                    <x-mailcoach::button-secondary x-on:click="$store.modals.open('preview')" :label="__('mailcoach - Preview')"/>
                    <x-mailcoach::button-secondary x-on:click="$store.modals.open('send-test')" :label="__('mailcoach - Send Test')"/>
                </div>

                <x-mailcoach::preview-modal :title="__('mailcoach - Preview') . ' - ' . $mail->subject" :html="$mail->html" />

                <x-mailcoach::modal :title="__('mailcoach - Send Test')" name="send-test">
                    @include('mailcoach::app.automations.mails.partials.test')
                </x-mailcoach::modal>
            @endif
        </dd>

        <dt>
            <span class="inline-flex gap-2 items-center">
                <span>
                    {{ __('mailcoach - Links') }}
                </span>
                <x-mailcoach::rounded-icon type="neutral" icon="fas fa-link"/>
            </span>
        </dt>

        <dd>
            @if (count($links))
                <p class="markup-code">
                    {{ __("mailcoach - The following links were found in your mail, make sure they are valid.") }}
                </p>
                <ul class="grid gap-2">
                    @foreach ($links as $url)
                        <li>
                            <a target="_blank" class="link" href="{{ $url }}">{{ $url }}</a><br>
                            <span class="mb-2 tag-neutral">{{ \Spatie\Mailcoach\Domain\Shared\Support\LinkHasher::hash($mail, $url) }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="markup-code">
                    {{ __("mailcoach - No links were found in your mail.") }}
                </p>
            @endif
        </dd>

        <dt>
            <span class="inline-flex gap-2 items-center">
                <span>
                    {{ __('mailcoach - Tags') }}
                </span>
                <x-mailcoach::rounded-icon type="neutral" icon="fas fa-tag"/>
            </span>
        </dt>

        <dd>
            <p class="markup-code">
                {{ __("mailcoach - The following tags will be added to subscribers when they open or click the mail:") }}
            </p>
            <ul class="flex space-x-2">
                <li class="tag-neutral">{{ "automation-mail-{$mail->id}-opened" }}</li>
                <li class="tag-neutral">{{ "automation-mail-{$mail->id}-clicked" }}</li>
            </ul>
        </dd>

        @if ($mail->isReady() && $mail->sendsCount() > 0)
            <div class="flex alert alert-info mt-6">
                <div class="mr-2">
                    <i class="fas fa-sync fa-spin text-blue-500"></i>
                </div>
                <div class="flex justify-between items-center w-full">
                    <p>
                        <span class="inline-block">{{ __('mailcoach - Automation mail') }}</span>
                        <a class="inline-block" target="_blank"
                           href="{{ $mail->webviewUrl() }}">{{ $mail->name }}</a>

                        {{ __('mailcoach - has been sent to :sendsCount :subscriber', [
                            'sendsCount' => $mail->sendsCount(),
                            'subscriber' => trans_choice('mailcoach - subscriber|subscribers', $mail->sendsCount())
                        ]) }}
                    </p>
                </div>
            </div>
        @endif
    </dl>
</div>
