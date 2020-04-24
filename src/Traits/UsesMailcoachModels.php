<?php

namespace Spatie\Mailcoach\Traits;

use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Models\Subscriber;

trait UsesMailcoachModels
{
    public function getCampaignClass(): string
    {
        return config('mailcoach.models.campaign', Campaign::class);
    }

    public function getEmailListClass(): string
    {
        return config('mailcoach.models.email_list', EmailList::class);
    }

    public function getSubscriberClass(): string
    {
        return config('mailcoach.models.subscriber', Subscriber::class);
    }
}
