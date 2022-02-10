<div class="mt-6 grid grid-cols-3 gap-6 justify-start items-end max-w-xl">
    @if ($mail->track_opens)
        <x-mailcoach::statistic :href="route('mailcoach.automations.mails.opens', $mail)" class="col-start-1"
                     numClass="text-4xl font-semibold" :stat="number_format($mail->unique_open_count)" :label="__('mailcoach - Unique Opens')"/>
        <x-mailcoach::statistic :stat="number_format($mail->open_count)" :label="__('mailcoach - Opens')"/>
        <x-mailcoach::statistic :stat="$mail->open_rate / 100" :label="__('mailcoach - Open Rate')" suffix="%"/>
    @else
        <div class="col-start-1 col-span-3">
            <div class="text-4xl font-semibold">–</div>
            <div class="text-sm">{{ __('mailcoach - Opens not tracked') }}</div>
        </div>
    @endif

    @if($mail->track_clicks)
        <x-mailcoach::statistic :href="route('mailcoach.automations.mails.clicks', $mail)" class="col-start-1"
                     numClass="text-4xl font-semibold" :stat="number_format($mail->unique_click_count)" :label="__('mailcoach - Unique Clicks')"/>
        <x-mailcoach::statistic :stat="number_format($mail->click_count)" :label="__('mailcoach - Clicks')"/>
        <x-mailcoach::statistic :stat="$mail->click_rate / 100" :label="__('mailcoach - Click Rate')" suffix="%"/>
    @else
        <div class="col-start-1 col-span-3">
            <div class="text-4xl font-semibold">–</div>
            <div class="text-sm">{{ __('mailcoach - Clicks not tracked') }}</div>
        </div>
    @endif

    <x-mailcoach::statistic :href="route('mailcoach.automations.mails.unsubscribes', $mail)" numClass="text-4xl font-semibold"
                 :stat="number_format($mail->unsubscribe_count)" :label="__('mailcoach - Unsubscribes')"/>
    <x-mailcoach::statistic :stat="$mail->unsubscribe_rate / 100" :label="__('mailcoach - Unsubscribe Rate')" suffix="%"/>

    <x-mailcoach::statistic :href="route('mailcoach.automations.mails.outbox', $mail) . '?filter[type]=bounced'"
                 class="col-start-1" numClass="text-4xl font-semibold" :stat="number_format($mail->bounce_count)"
                 :label="__('mailcoach - Bounces')"/>
    <x-mailcoach::statistic :stat="$mail->bounce_rate / 100" :label="__('mailcoach - Bounce Rate')" suffix="%"/>
</div>
