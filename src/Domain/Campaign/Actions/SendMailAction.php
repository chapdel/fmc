<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions;

use Exception;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignMailSentEvent;
use Spatie\Mailcoach\Domain\Shared\Mails\MailcoachMail;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
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
        $personalizeSubjectAction = Config::getCampaignActionClass('personalize_subject', PersonalizeSubjectAction::class);
        $personalisedSubject = $personalizeSubjectAction->execute($pendingSend->campaign->subject, $pendingSend);

        /** @var \Spatie\Mailcoach\Domain\Campaign\Actions\PersonalizeHtmlAction $personalizeHtmlAction */
        $personalizeHtmlAction = Config::getCampaignActionClass('personalize_html', PersonalizeHtmlAction::class);
        $personalisedHtml = $personalizeHtmlAction->execute(
            $pendingSend->campaign->email_html,
            $pendingSend,
        );

        /** @var \Spatie\Mailcoach\Domain\Campaign\Actions\ConvertHtmlToTextAction $convertHtmlToTextAction */
        $convertHtmlToTextAction = Config::getCampaignActionClass('convert_html_to_text', ConvertHtmlToTextAction::class);
        $personalisedText = $convertHtmlToTextAction->execute($personalisedHtml);

        $mailcoachMail = app(MailcoachMail::class);

        /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign $campaign */
        $campaign = $pendingSend->campaign;

        $mailcoachMail
            ->setSend($pendingSend)
            ->subject($personalisedSubject)
            ->setFrom(
                $campaign->from_email
                ?? $campaign->emailList->default_from_email
                ?? optional($pendingSend)->subscriber->emailList->default_from_email,
                $campaign->from_name
                ?? $campaign->emailList->default_from_name
                ?? optional($pendingSend)->subscriber->emailList->default_from_name
                ?? null
            )
            ->setHtmlContent($personalisedHtml)
            ->setTextContent($personalisedText)
            ->setHtmlView('mailcoach::mails.campaignHtml')
            ->setTextView('mailcoach::mails.campaignText')
            ->withSwiftMessage(function (Swift_Message $message) use ($pendingSend) {
                $message->getHeaders()->addTextHeader('X-MAILCOACH', 'true');

                /** Postmark specific header */
                $message->getHeaders()->addTextHeader('X-PM-Metadata-send-uuid', $pendingSend->uuid);
            });

        $replyTo = $this->campaign->reply_to_email
            ?? $this->campaign->emailList->reply_to_email
            ?? optional($pendingSend)->subscriber->emailList->reply_to_email
            ?? null;

        if ($replyTo) {
            $replyToName = $this->campaign->reply_to_name
                ?? $this->campaign->emailList->default_reply_to_name
                ?? optional($pendingSend)->subscriber->emailList->default_reply_to_name
                ?? null;
            $mailcoachMail->setReplyTo($replyTo, $replyToName);
        }

        $mailer = $campaign->emailList->campaign_mailer
            ?? config('mailcoach.campaigns.mailer')
            ?? config('mailcoach.mailer')
            ?? config('mail.default');

        Mail::mailer($mailer)
            ->to($pendingSend->subscriber->email)
            ->send($mailcoachMail);

        $pendingSend->markAsSent();

        event(new CampaignMailSentEvent($pendingSend));
    }
}
