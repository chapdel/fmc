<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Mails;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Mails\Concerns\StoresMail;
use Spatie\Mailcoach\Domain\TransactionalMail\Mails\Concerns\UsesMailcoachTemplate;
use Spatie\Mailcoach\Mailcoach;
use Symfony\Component\Mime\Email;

class TransactionalMail extends Mailable
{
    use SerializesModels;
    use StoresMail;
    use UsesMailcoachTemplate;

    private string $mailName;

    private array $replacements;

    private array $embeddedAttachments;

    private array $attachedAttachments;

    private array $fields;

    public function __construct(
        string $mailName,
        string $subject,
        array|string $from,
        array $to,
        array $cc = [],
        array $bcc = [],
        string $mailer = null,
        array $replacements = [],
        array $attachments = [],
        bool $store = true,
    ) {
        $this->mailName = $mailName;
        $this->replacements = $replacements;

        $this->setTransactionalHeader();

        $this->embeddedAttachments = array_filter(
            $attachments,
            fn ($attachment) => ! is_null($attachment['content_id'] ?? null),
        );

        $this->attachedAttachments = array_filter(
            $attachments, fn ($attachment) => is_null($attachment['content_id'] ?? null),
        );

        $this
            ->when($store, function (TransactionalMail $mail) {
                $mail->store();
            })
            ->from($from)
            ->to($to)
            ->cc($cc)
            ->bcc($bcc)
            ->subject($subject)
            ->mailer($mailer ?? Mailcoach::defaultTransactionalMailer())
            ->view('mailcoach::mails.transactionalMails.mail');
    }

    public function build()
    {
        $this->template(
            $this->mailName,
            $this->replacements,
        );

        $this->withSymfonyMessage(function (Email $email) {
            foreach ($this->embeddedAttachments as $embeddedAttachment) {
                $email->embed(
                    body: $embeddedAttachment['content'],
                    name: $embeddedAttachment['name'],
                    contentType: $embeddedAttachment['content_type'],
                );
            }

            foreach ($this->attachedAttachments as $attachedAttachment) {
                $email->attach(
                    body: $attachedAttachment['content'],
                    name: $attachedAttachment['name'],
                    contentType: $attachedAttachment['content_type'],
                );
            }
        });
    }
}
