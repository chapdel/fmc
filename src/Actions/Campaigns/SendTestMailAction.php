<?php

namespace Spatie\Mailcoach\Actions\Campaigns;

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Models\Campaign;
use Swift_Message;

class SendTestMailAction
{
    public function execute(Campaign $campaign, string $email)
    {
        $campaignMailable = $campaign->getMailable()
            ->setCampaign($campaign)
            ->setHtmlContent($campaign->htmlWithInlinedCss())
            ->subject($campaign->subject)
            ->withSwiftMessage(function (Swift_Message $message) {
                $message->getHeaders()->addTextHeader('X-MAILCOACH', true);
            });

        Mail::to($email)->send($campaignMailable);
    }
}
