<?php

namespace Spatie\Mailcoach\Actions\Campaigns;

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Support\Config;
use Swift_Message;

class SendTestMailAction
{
    public function execute(Campaign $campaign, string $email)
    {
        $html = $campaign->htmlWithInlinedCss();

        $convertHtmlToTextAction = Config::getActionClass('convert_html_to_text', ConvertHtmlToTextAction::class);

        $text = $convertHtmlToTextAction->execute($html);

        $campaignMailable = $campaign->getMailable()
            ->setCampaign($campaign)
            ->setHtmlContent($html)
            ->setTextContent($text)
            ->subject($campaign->subject)
            ->withSwiftMessage(function (Swift_Message $message) {
                $message->getHeaders()->addTextHeader('X-MAILCOACH', true);
            });

        Mail::mailer($campaign->emailList->campaign_mailer)
            ->to($email)
            ->send($campaignMailable);
    }
}
