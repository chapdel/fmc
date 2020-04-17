<?php

namespace Spatie\Mailcoach\Traits;

use Spatie\Mailcoach\MailcoachRegistrar;

trait UsesCampaign
{
    private $campaignClass;

    public function getCampaignClass()
    {
        if (! isset($this->campaignClass)) {
            $this->campaignClass = app(MailcoachRegistrar::class)->getCampaignClass();
        }

        return $this->campaignClass;
    }
}
