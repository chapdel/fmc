<?php

namespace Spatie\Mailcoach\Traits;

trait UsesMailcoachModels
{
    public function getCampaignClass(): string
    {
        return config('mailcoach.models.campaign');
    }

    public function getEmailListClass(): string
    {
        return config('mailcoach.models.email_list');
    }

    public function getSubscriberClass(): string
    {
        return config('mailcoach.models.campaign');
    }
}
