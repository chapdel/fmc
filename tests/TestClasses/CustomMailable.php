<?php

namespace Spatie\Mailcoach\Tests\TestClasses;

use Spatie\Mailcoach\Mails\CampaignMail;

class CustomMailable extends CampaignMail
{
    public function build()
    {
        return $this->markdown('mailcoach::mails.campaignText');
    }
}
