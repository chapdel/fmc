<?php

namespace Spatie\Mailcoach\Mails;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Spatie\Mailcoach\Models\Concerns\Campaign as CampaignConcern;

class CampaignSentMail extends Mailable implements ShouldQueue
{
    public string $theme = 'mailcoach::mails.layout.mailcoach';

    public CampaignConcern $campaign;

    public string $summaryUrl;

    public function __construct(CampaignConcern $campaign)
    {
        $this->campaign = $campaign;

        $this->summaryUrl = route('mailcoach.campaigns.summary', $this->campaign);
    }

    public function build()
    {
        $this
            ->from(
                $this->campaign->emailList->default_from_email,
                $this->campaign->emailList->default_from_name
            )
            ->subject("The campaign named '{$this->campaign->name}' has been sent")
            ->markdown('mailcoach::mails.campaignSent');
    }
}
