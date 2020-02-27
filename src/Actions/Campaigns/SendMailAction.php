<?php

namespace Spatie\Mailcoach\Actions\Campaigns;

use Exception;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Events\CampaignMailSentEvent;
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

        $personalizeHtmlAction = Config::getActionClass('personalize_html', PersonalizeHtmlAction::class);

        $personalisedHtml = $personalizeHtmlAction->execute(
            $pendingSend->campaign->email_html,
            $pendingSend,
            );

        $convertHtmlToTextAction = Config::getActionClass('convert_html_to_text', ConvertHtmlToTextAction::class);

        $personalisedText = $convertHtmlToTextAction->execute($personalisedHtml);

        /** @var \Spatie\Mailcoach\Mails\CampaignMail $campaignMail */
        $campaignMail = $pendingSend->campaign->getMailable();

        $campaignMail
            ->setSend($pendingSend)
            ->setHtmlContent($personalisedHtml)
            ->setTextContent($personalisedText)
            ->subject($pendingSend->campaign->subject)
            ->withSwiftMessage(function (Swift_Message $message) use ($pendingSend) {
                $message->getHeaders()->addTextHeader('X-MAILCOACH', true);

                /** Postmark specific header */
                $message->getHeaders()->addTextHeader('X-PM-SEND-UUID', $pendingSend->uuid);
            });

        Mail::to($pendingSend->subscriber->email)->send($campaignMail);

        $pendingSend->markAsSent();

        event(new CampaignMailSentEvent($pendingSend));
    }
}
