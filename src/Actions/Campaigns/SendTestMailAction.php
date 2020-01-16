<?php

namespace Spatie\Mailcoach\Actions\Campaigns;

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Models\Campaign;

class SendTestMailAction
{
    public function execute(Campaign $campaign, string $email)
    {
        $campaignMailable = $campaign->getMailable()
            ->setCampaign($campaign)
            ->setHtmlContent($campaign->htmlWithInlinedCss())
            ->subject($campaign->subject);

        Mail::to($email)->send($campaignMailable);
    }
}
