<?php

namespace Spatie\Mailcoach\Actions\Campaigns;

use Exception;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Events\CampaignMailSentEvent;
use Spatie\Mailcoach\Mails\CampaignMail;
use Spatie\Mailcoach\Models\Send;
use Spatie\Mailcoach\Support\Config;
use Swift_Message;

class SendMailAction
{
    public function execute(Send $pendingSend)
    {
        try {
            $this->sendMail($pendingSend);
        } catch (Exception $exception) {
            report($exception);

            $pendingSend->markAsFailed($exception->getMessage());
        }
    }

    protected function sendMail(Send $pendingSend)
    {
        if ($pendingSend->wasAlreadySent()) {
            return;
        }

        /** @var \Spatie\Mailcoach\Actions\Campaigns\PersonalizeSubjectAction $personalizeSubjectAction */
        $personalizeSubjectAction = Config::getActionClass('personalize_subject', PersonalizeSubjectAction::class);
        $personalisedSubject = $personalizeSubjectAction->execute($pendingSend->campaign->subject, $pendingSend);

        /** @var \Spatie\Mailcoach\Actions\Campaigns\PersonalizeHtmlAction $personalizeHtmlAction */
        $personalizeHtmlAction = Config::getActionClass('personalize_html', PersonalizeHtmlAction::class);
        $personalisedHtml = $personalizeHtmlAction->execute(
            $pendingSend->campaign->email_html,
            $pendingSend,
        );

        /** @var \Spatie\Mailcoach\Actions\Campaigns\ConvertHtmlToTextAction $convertHtmlToTextAction */
        $convertHtmlToTextAction = Config::getActionClass('convert_html_to_text', ConvertHtmlToTextAction::class);
        $personalisedText = $convertHtmlToTextAction->execute($personalisedHtml);

        /** @var \Spatie\Mailcoach\Mails\CampaignMail $campaignMail */
        $campaignMail = app(CampaignMail::class);

        /** @var \Spatie\Mailcoach\Models\Campaign $campaign */
        $campaign = $pendingSend->campaign;

        $campaignMail
            ->setSend($pendingSend)
            ->subject($personalisedSubject)
            ->setHtmlContent($personalisedHtml)
            ->setTextContent($personalisedText)
            ->withSwiftMessage(function (Swift_Message $message) use ($pendingSend) {
                $message->getHeaders()->addTextHeader('X-MAILCOACH', true);

                /** Postmark specific header */
                $message->getHeaders()->addTextHeader('X-PM-Metadata-send-uuid', $pendingSend->uuid);
            });

        $mailer = $campaign->emailList->campaign_mailer
            ?? config('mailcoach.campaign_mailer')
            ?? config('mailcoach.mailer')
            ?? config('mail.default');

        Mail::mailer($mailer)
            ->to($pendingSend->subscriber->email)
            ->send($campaignMail);

        $pendingSend->markAsSent();

        event(new CampaignMailSentEvent($pendingSend));
    }
}
