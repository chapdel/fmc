<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Mails;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Mails\Concerns\StoresMail;
use Spatie\Mailcoach\Domain\TransactionalMail\Mails\Concerns\UsesMailcoachTemplate;
use Spatie\Mailcoach\Mailcoach;

class TransactionalMail extends Mailable
{
    use SerializesModels;
    use StoresMail;
    use UsesMailcoachTemplate;

    private string $mailName;

    private array $replacements;

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
        bool $store = true,
    ) {
        $this->mailName = $mailName;
        $this->replacements = $replacements;

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
    }
}
