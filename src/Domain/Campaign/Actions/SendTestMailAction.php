<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Campaign\Mails\CampaignMail;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Support\Config;
use Swift_Message;

class SendTestMailAction
{
    public function execute(Campaign $campaign, string $email): void
    {
        $html = $campaign->htmlWithInlinedCss();

        $convertHtmlToTextAction = Config::getActionClass('convert_html_to_text', ConvertHtmlToTextAction::class);

        $text = $convertHtmlToTextAction->execute($html);

        $campaignMailable = app(CampaignMail::class)
            ->setCampaign($campaign)
            ->setHtmlContent($html)
            ->setTextContent($text)
            ->subject("[Test] {$campaign->subject}")
            ->withSwiftMessage(function (Swift_Message $message) {
                $message->getHeaders()->addTextHeader('X-MAILCOACH', 'true');
                $message->getHeaders()->addTextHeader('X-Entity-Ref-ID', Str::uuid()->toString());
            });

        $mailer = $campaign->emailList->campaign_mailer
            ?? config('mailcoach.campaign_mailer')
            ?? config('mailcoach.mailer')
            ?? config('mail.default');

        Mail::mailer($mailer)
            ->to($email)
            ->send($campaignMailable);
    }
}
