<?php

namespace Spatie\Mailcoach\Listeners;

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Events\CampaignSentEvent;
use Spatie\Mailcoach\Mails\CampaignSentMail;

class SendCampaignSentEmail
{
    public function handle(CampaignSentEvent $event)
    {
        $campaign = $event->campaign;

        if (! $campaign->emailList->report_campaign_sent) {
            return;
        }

        Mail::mailer(config('mailcoach.mailer') ?? config('mail.default'))
            ->to($campaign->emailList->campaignReportRecipients())
            ->queue(new CampaignSentMail($campaign));
    }
}
