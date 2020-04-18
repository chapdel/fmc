<?php

namespace Spatie\Mailcoach\Traits;

trait UsesCampaign
{
    private string $campaignClass;

    public function getCampaignClass(): string
    {
        return config('mailcoach.models.campaign');
    }
}
