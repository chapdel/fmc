<?php

namespace Spatie\Mailcoach\Mails;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Spatie\Mailcoach\Models\Campaign;

class CampaignSummaryMail extends Mailable implements ShouldQueue
{
    public $theme = 'mailcoach::mails.layout.mailcoach';

    public Campaign $campaign;

    public string $summaryUrl;

    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;

        $this->summaryUrl = route('mailcoach.campaigns.summary', $this->campaign);
    }

    public function build(): self
    {
        return $this
            ->from(
                $this->campaign->emailList->default_from_email,
                $this->campaign->emailList->default_from_name
            )
            ->subject(__("A summary of the ':campaignName' campaign", ['campaignName' => $this->campaign->name]))
            ->markdown('mailcoach::mails.campaignSummary');
    }
}
