<?php

namespace Spatie\Mailcoach\Domain\Campaign\Listeners;

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Campaign\Enums\TagType;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignLinkClickedEvent;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignOpenedEvent;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignSentEvent;
use Spatie\Mailcoach\Domain\Campaign\Mails\CampaignSentMail;

class AddCampaignClickedTag
{
    public function handle(CampaignLinkClickedEvent $event)
    {
        $campaign = $event->campaignClick->link->campaign;
        $subscriber = $event->campaignClick->subscriber;

        $subscriber->addTag("campaign-{$campaign->id}-clicked", TagType::MAILCOACH);
    }
}
