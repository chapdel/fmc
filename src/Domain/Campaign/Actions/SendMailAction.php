<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions;

use Exception;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignMailSentEvent;
use Spatie\Mailcoach\Domain\Campaign\Mails\CampaignMail;
use Spatie\Mailcoach\Domain\Campaign\Models\Send;
use Spatie\Mailcoach\Domain\Shared\Support\Config;
use Swift_Message;

class SendMailAction
{
    public function execute(Send $pendingSend): void
    {
        try {
            $this->sendMail($pendingSend);
        } catch (Exception $exception) {
            report($exception);

            $pendingSend->markAsFailed($exception->getMessage());
        }
    }

    protected function sendMail(Send $pendingSend): void
    {
        if ($pendingSend->wasAlreadySent()) {
            return;
        }

        /** @var \Spatie\Mailcoach\Domain\Campaign\Actions\PersonalizeSubjectAction $personalizeSubjectAction */
        $personalizeSubjectAction = Config::getActionClass('personalize_subject', PersonalizeSubjectAction::class);
        $personalisedSubject = $personalizeSubjectAction->execute($pendingSend->campaign->subject, $pendingSend);

        /** @var \Spatie\Mailcoach\Domain\Campaign\Actions\PersonalizeHtmlAction $personalizeHtmlAction */
        $personalizeHtmlAction = Config::getActionClass('personalize_html', PersonalizeHtmlAction::class);
        $personalisedHtml = $personalizeHtmlAction->execute(
            $pendingSend->campaign->email_html,
            $pendingSend,
        );

        /** @var \Spatie\Mailcoach\Domain\Campaign\Actions\ConvertHtmlToTextAction $convertHtmlToTextAction */
        $convertHtmlToTextAction = Config::getActionClass('convert_html_to_text', ConvertHtmlToTextAction::class);
        $personalisedText = $convertHtmlToTextAction->execute($personalisedHtml);

        $campaignMail = app(CampaignMail::class);

        /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign $campaign */
        $campaign = $pendingSend->campaign;

        $campaignMail
            ->setSend($pendingSend)
            ->subject($personalisedSubject)
            ->setHtmlContent($personalisedHtml)
            ->setTextContent($personalisedText)
            ->withSwiftMessage(function (Swift_Message $message) use ($pendingSend) {
                $message->getHeaders()->addTextHeader('X-MAILCOACH', 'true');

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
