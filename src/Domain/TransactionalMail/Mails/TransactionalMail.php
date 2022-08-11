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

    private string $templateName;

    public function __construct(
        string $templateName,
        string $subject,
        array|string $from,
        array $to,
        array $cc = [],
        array $bcc = [],
        string $mailer = null,
        array $replacements = [],
    ) {
        $this->templateName = $templateName;
        $this->replacements = $replacements;

        $this
            ->store()
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
        $this->template($this->templateName, $this->replacements);
    }
}
