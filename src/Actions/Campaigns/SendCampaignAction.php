<?php

namespace Spatie\Mailcoach\Actions\Campaigns;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Jobs\MarkCampaignAsSentJob;
use Spatie\Mailcoach\Jobs\SendMailJob;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Models\Send;
use Spatie\Mailcoach\Models\Subscriber;
use Spatie\Mailcoach\Support\Config;
use Spatie\Mailcoach\Support\Segments\Segment;

class SendCampaignAction
{
    public function execute(Campaign $campaign)
    {
        if ($campaign->wasAlreadySent()) {
            return;
        }

        $this
            ->prepareEmailHtml($campaign)
            ->prepareWebviewHtml($campaign)
            ->sendMailsForCampaign($campaign);
    }

    protected function prepareEmailHtml(Campaign $campaign)
    {
        /** @var \Spatie\Mailcoach\Actions\Campaigns\PrepareEmailHtmlAction $prepareEmailHtmlAction */
        $prepareEmailHtmlAction = Config::getActionClass('prepare_email_html', PrepareEmailHtmlAction::class);

        $prepareEmailHtmlAction->execute($campaign);

        return $this;
    }

    protected function prepareWebviewHtml(Campaign $campaign)
    {
        /** @var \Spatie\Mailcoach\Actions\Campaigns\PrepareWebviewHtmlAction $prepareWebviewHtmlAction */
        $prepareWebviewHtmlAction = Config::getActionClass('prepare_webview_html', PrepareWebviewHtmlAction::class);

        $prepareWebviewHtmlAction->execute($campaign);

        return $this;
    }

    protected function sendMailsForCampaign(Campaign $campaign)
    {
        $campaign->update(['segment_description' => $campaign->getSegment()->description()]);

        $subscribersQuery = $campaign->baseSubscribersQuery();

        $segment = $campaign->getSegment();

        $segment->subscribersQuery($subscribersQuery);

        $campaign->update(['sent_to_number_of_subscribers' => $subscribersQuery->count()]);

        $jobs = $subscribersQuery->cursor()->map(function (Subscriber $subscriber) use ($campaign, $segment) {
            return $this->sendMail($campaign, $subscriber, $segment);
        })->filter()->toArray();

        dispatch(function () {
            return;
        })->chain(array_merge($jobs, [
            new MarkCampaignAsSentJob($campaign),
        ]));
    }

    protected function sendMail(Campaign $campaign, Subscriber $subscriber, Segment $segment): ?ShouldQueue
    {
        if (! $segment->shouldSend($subscriber)) {
            return null;
        }

        if (! $this->isValidSubscriptionForEmailList($subscriber, $campaign->emailList)) {
            return null;
        }

        $pendingSend = $this->createSend($campaign, $subscriber);

        return new SendMailJob($pendingSend);
    }

    protected function createSend(Campaign $campaign, Subscriber $subscriber): Send
    {
        /** @var \Spatie\Mailcoach\Models\Send $pendingSend */
        $pendingSend = $campaign->sends()
            ->where('subscriber_id', $subscriber->id)
            ->first();

        if ($pendingSend) {
            return $pendingSend;
        }

        return $campaign->sends()->create([
            'subscriber_id' => $subscriber->id,
            'uuid' => (string)Str::uuid(),
        ]);
    }

    protected function isValidSubscriptionForEmailList(Subscriber $subscriber, EmailList $emailList): bool
    {
        if (! $subscriber->isSubscribed()) {
            return false;
        }

        if ((int)$subscriber->email_list_id !== (int)$emailList->id) {
            return false;
        }

        return true;
    }
}
