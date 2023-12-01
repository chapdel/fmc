<?php /** @var \Spatie\Mailcoach\Domain\Automation\Models\AutomationMail $mail */ ?>
<x-mailcoach::card>
    <div class="grid grid-cols-3 xl:grid-cols-5 gap-12 justify-start items-start">
        <x-mailcoach::statistic
            :href="route('mailcoach.automations.mails.outbox', $mail)"
            :stat="number_format($mail->sentToNumberOfSubscribers())"
            :label="__mc('Recipients')"
            :progress="$mail->sentToNumberOfSubscribers() > 0 ? 100 : 0"
        />

        @if ($mail->openCount())
            <x-mailcoach::statistic
                :href="route('mailcoach.automations.mails.opens', $mail)"
                :stat="$mail->openRate() / 100"
                :label="__mc('Open Rate')"
                suffix="%"
                :progress="$mail->openRate() / 100"
                :progress-tooltip="$mail->uniqueOpenCount()"
            />
        @else
            <div class="">
                <div class="leading-none text-4xl font-semibold">–</div>
                <div class="text-sm">{{ __mc('No opens tracked') }}</div>
            </div>
        @endif

        @if($mail->clickCount())
            <x-mailcoach::statistic
                :href="route('mailcoach.automations.mails.clicks', $mail)"
                :stat="$mail->clickRate() / 100"
                :label="__mc('Click Rate')"
                suffix="%"
                :progress="$mail->clickRate() / 100"
                :progress-tooltip="$mail->uniqueClickCount()"
            />
        @else
            <div class="">
                <div class="leading-none text-4xl font-semibold">–</div>
                <div class="text-sm">{{ __mc('No clicks tracked') }}</div>
            </div>
        @endif

        <x-mailcoach::statistic
            :href="route('mailcoach.automations.mails.unsubscribes', $mail)"
            :stat="$mail->unsubscribeRate() / 100"
            :label="__mc('Unsubscribe Rate')"
            suffix="%"
            :progress="$mail->unsubscribeRate() / 100"
            :progress-tooltip="$mail->unsubscribeCount()"
            progress-class="bg-red-500"
        />

        <x-mailcoach::statistic
            :href="route('mailcoach.automations.mails.outbox', $mail) . '?filter[type][value]=bounced&tableFilters[type][value]=bounced'"
            :stat="$mail->bounceRate() / 100"
            :label="__mc('Bounce Rate')"
            suffix="%"
            :progress="$mail->bounceRate() / 100"
            :progress-tooltip="$mail->bounceCount()"
            progress-class="bg-red-500"
        />
    </div>
</x-mailcoach::card>
