<?php

namespace Spatie\Mailcoach\Traits;

trait UsesMailcoachModels
{
    public function getCampaignClass(): string
    {
        return config('mailcoach.models.campaign', \Spatie\Mailcoach\Models\Campaign::class);
    }

    public function getEmailListClass(): string
    {
        return config('mailcoach.models.email_list', \Spatie\Mailcoach\Models\EmailList::class);
    }

    public function getSubscriberClass(): string
    {
        return config('mailcoach.models.campaign', \Spatie\Mailcoach\Models\Subscriber::class);
    }
}
