<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Mails;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Mails\Concerns\StoresMail;
use Spatie\Mailcoach\Domain\TransactionalMail\Mails\Concerns\UsesMailcoachTemplate;

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
    ) {
        $this->templateName = $templateName;

        $this
            ->store()
            ->from($from)
            ->to($to)
            ->cc($cc)
            ->bcc($bcc)
            ->subject($subject)
            ->mailer($mailer ?? config('mailcoach.transactional.mailer') ?? config('mail.default'))
            ->view('mailcoach::mails.transactionalMails.mail');
    }

    public function build()
    {
        $this->template($this->templateName);
    }
}
