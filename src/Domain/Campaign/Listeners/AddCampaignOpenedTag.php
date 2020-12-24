<?php

namespace Spatie\Mailcoach\Domain\Campaign\Listeners;

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Campaign\Enums\TagType;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignOpenedEvent;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignSentEvent;
use Spatie\Mailcoach\Domain\Campaign\Mails\CampaignSentMail;

class AddCampaignOpenedTag
{
    public function handle(CampaignOpenedEvent $event)
    {
        $campaign = $event->campaignOpen->campaign;
        $subscriber = $event->campaignOpen->subscriber;

        $subscriber->addTag("campaign-{$campaign->id}-opened", TagType::MAILCOACH);
    }
}
