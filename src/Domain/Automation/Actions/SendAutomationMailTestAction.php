<?php

namespace Spatie\Mailcoach\Domain\Automation\Actions;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Shared\Mails\MailcoachMail;
use Spatie\Mailcoach\Mailcoach;
use Symfony\Component\Mime\Email;

class SendAutomationMailTestAction
{
    public function execute(AutomationMail $mail, string $email): void
    {
        $html = $mail->htmlWithInlinedCss();

        $convertHtmlToTextAction = Mailcoach::getAutomationActionClass('convert_html_to_text', ConvertHtmlToTextAction::class);

        $text = $convertHtmlToTextAction->execute($html);

        $mailable = resolve(MailcoachMail::class)
            ->setFrom($mail->getFromEmail(), $mail->getFromName())
            ->setHtmlContent($html)
            ->setTextContent($text)
            ->setHtmlView('mailcoach::mails.automation.automationHtml')
            ->setTextView('mailcoach::mails.automation.automationText')
            ->subject("[Test] {$mail->subject}")
            ->withSymfonyMessage(function (Email $message) {
                $message->getHeaders()->addTextHeader('X-MAILCOACH', 'true');
                $message->getHeaders()->addTextHeader('Precedence', 'Bulk');
                $message->getHeaders()->addTextHeader('X-Entity-Ref-ID', Str::uuid()->toString());
            });

        if ($mail->reply_to_email) {
            $mailable->setReplyTo($mail->reply_to_email, $mail->reply_to_name);
        }

        Mail::mailer(Mailcoach::defaultAutomationMailer())
            ->to($email)
            ->send($mailable);
    }
}
