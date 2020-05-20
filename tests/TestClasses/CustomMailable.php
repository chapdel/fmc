<?php

namespace Spatie\Mailcoach\Tests\TestClasses;

use Spatie\Mailcoach\Mails\CampaignMail;

class CustomMailable extends CampaignMail
{
    public function build()
    {
        return $this
            ->from(
                $this->campaign->from_email ?? $this->campaign->emailList->default_from_email,
                $this->campaign->from_name ?? $this->campaign->emailList->default_from_name ?? null
            )
            ->subject($this->subject)
            ->view('mailcoach::mails.campaignHtml')
            ->text('mailcoach::mails.campaignText')
            ->addUnsubscribeHeaders()
            ->storeTransportMessageId();
    }
}
