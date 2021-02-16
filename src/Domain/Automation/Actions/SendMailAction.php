<?php

namespace Spatie\Mailcoach\Domain\Automation\Actions;

use Exception;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Automation\Events\AutomationMailSentEvent;
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

        /** @var \Spatie\Mailcoach\Domain\Automation\Actions\PersonalizeSubjectAction $personalizeSubjectAction */
        $personalizeSubjectAction = Config::getAutomationActionClass('personalize_subject', PersonalizeSubjectAction::class);
        $personalisedSubject = $personalizeSubjectAction->execute($pendingSend->campaign->subject, $pendingSend);

        /** @var \Spatie\Mailcoach\Domain\Automation\Actions\PersonalizeHtmlAction $personalizeHtmlAction */
        $personalizeHtmlAction = Config::getAutomationActionClass('personalize_html', PersonalizeHtmlAction::class);
        $personalisedHtml = $personalizeHtmlAction->execute(
            $pendingSend->campaign->email_html,
            $pendingSend,
        );

        /** @var \Spatie\Mailcoach\Domain\Automation\Actions\ConvertHtmlToTextAction $convertHtmlToTextAction */
        $convertHtmlToTextAction = Config::getAutomationActionClass('convert_html_to_text', ConvertHtmlToTextAction::class);
        $personalisedText = $convertHtmlToTextAction->execute($personalisedHtml);

        $mailcoachMail = app(MailcoachMail::class);

        $automationMail = $pendingSend->automationMail;

        $mailcoachMail
            ->setSend($pendingSend)
            ->subject($personalisedSubject)
            ->setFrom($automationMail->from_email, $automationMail->from_name)
            ->setReplyTo($automationMail->reply_to_email, $automationMail->reply_to_name)
            ->setHtmlContent($personalisedHtml)
            ->setTextContent($personalisedText)
            ->setHtmlView('mailcoach::mails.automation.automationHtml')
            ->setTextView('mailcoach::mails.automation.automationText')
            ->withSwiftMessage(function (Swift_Message $message) use ($pendingSend) {
                $message->getHeaders()->addTextHeader('X-MAILCOACH', 'true');

                /** Postmark specific header */
                $message->getHeaders()->addTextHeader('X-PM-Metadata-send-uuid', $pendingSend->uuid);
            });

        $mailer = config('mailcoach.automation.mailer')
            ?? config('mailcoach.mailer')
            ?? config('mail.default');

        Mail::mailer($mailer)
            ->to($pendingSend->subscriber->email)
            ->send($mailcoachMail);

        $pendingSend->markAsSent();

        event(new AutomationMailSentEvent($pendingSend));
    }
}
