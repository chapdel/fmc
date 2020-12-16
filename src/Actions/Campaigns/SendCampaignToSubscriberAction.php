<?php

namespace Spatie\Mailcoach\Actions\Campaigns;

use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Models\Subscriber;

class SendCampaignToSubscriberAction extends SendCampaignAction
{
    public function execute(Campaign $campaign, Subscriber $subscriber = null)
    {
        if ($campaign->wasAlreadySentToSubscriber($subscriber)) {
            return;
        }

        if (! $subscriber) {
            return;
        }

        $this
            ->prepareSubject($campaign)
            ->prepareEmailHtml($campaign)
            ->prepareWebviewHtml($campaign)
            ->sendMail($campaign, $subscriber);
    }

    protected function sendMail(Campaign $campaign, Subscriber $subscriber = null)
    {
        $campaign->update(['segment_description' => $campaign->getSegment()->description()]);

        dispatch($this->createSendMailJob($campaign, $subscriber->emailList, $subscriber));
    }
}
