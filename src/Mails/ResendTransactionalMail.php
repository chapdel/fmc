<?php

namespace Spatie\Mailcoach\Mails;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Mails\Concerns\StoresMail;
use Spatie\Mailcoach\Models\TransactionalMail;

class ResendTransactionalMail extends Mailable
{
    use SerializesModels;
    use StoresMail;

    public function __construct(
        public TransactionalMail $originalMail
    ) {}

    public function build()
    {
        $this
            ->from($this->convertPersonsToMailableFormat($this->originalMail->from))
            ->to($this->convertPersonsToMailableFormat($this->originalMail->to))
            ->cc($this->convertPersonsToMailableFormat($this->originalMail->cc))
            ->bcc($this->convertPersonsToMailableFormat($this->originalMail->bcc))
            ->subject($this->originalMail->subject)
            ->view('mailcoach::mails.transactionalMailResend');

        if ($this->originalMail->track_opens) {
            $this->trackOpens();
        }

        if ($this->originalMail->track_clicks) {
            $this->trackClicks();
        }

        $this->setMailableClassHeader($this->originalMail->mailable_class);
    }

    protected function convertPersonsToMailableFormat(array $persons): array
    {
        return collect($persons)
            ->mapWithKeys(function (array $person) {
                return [$person['email'] => $person['name'] ?? null];
            })
            ->toArray();

    }
}
