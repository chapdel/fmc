<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Mails\MailcoachMail;
use Spatie\Mailcoach\Mailcoach;
use Symfony\Component\Mime\Email;

class SendCampaignTestAction
{
    public function execute(Campaign $campaign, string $email): void
    {
        /** @var \Spatie\Mailcoach\Domain\Campaign\Actions\PrepareSubjectAction $prepareSubjectAction */
        $prepareSubjectAction = Mailcoach::getCampaignActionClass('prepare_subject', PrepareSubjectAction::class);
        $prepareSubjectAction->execute($campaign);

        /** @var \Spatie\Mailcoach\Domain\Campaign\Actions\PrepareEmailHtmlAction $prepareEmailHtmlAction */
        $prepareEmailHtmlAction = Mailcoach::getCampaignActionClass('prepare_email_html', PrepareEmailHtmlAction::class);
        $prepareEmailHtmlAction->execute($campaign);

        $convertHtmlToTextAction = Mailcoach::getCampaignActionClass('convert_html_to_text', ConvertHtmlToTextAction::class);
        $text = $convertHtmlToTextAction->execute($campaign->email_html);

        $campaignMailable = resolve(MailcoachMail::class)
            ->setSendable($campaign)
            ->setHtmlContent($campaign->email_html)
            ->setTextContent($text)
            ->subject("[Test] {$campaign->subject}")
            ->withSymfonyMessage(function (Email $message) {
                $message->getHeaders()->addTextHeader('X-MAILCOACH', 'true');
                $message->getHeaders()->addTextHeader('X-Entity-Ref-ID', Str::uuid()->toString());
            });

        Mail::mailer($campaign->getMailerKey())
            ->to($email)
            ->send($campaignMailable);
    }
}
