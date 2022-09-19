<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\TransactionalMails;

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\TransactionalMail\Mails\TransactionalMail;
use Spatie\Mailcoach\Http\Api\Controllers\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Http\Api\Requests\SendTransactionalMailRequest;
use Symfony\Component\Mime\Address;

class SendTransactionalMailController
{
    use RespondsToApiRequests;

    public function __invoke(SendTransactionalMailRequest $request)
    {
        $mail = new TransactionalMail(
            templateName: $request->get('template'),
            subject: $request->get('subject'),
            from: $request->get('from'),
            to: $this->normalizeEmailAddresses($request->get('to')),
            cc: $this->normalizeEmailAddresses($request->get('cc')),
            bcc: $this->normalizeEmailAddresses($request->get('bcc')),
            mailer: $request->mailer,
            replacements: $request->replacements(),
            fields: $request->fields(),
            store: $request->shouldStoreMail(),
        );

        Mail::send($mail);

        return $this->respondOk();
    }

    private function normalizeEmailAddresses(?string $addresses): array
    {
        if (is_null($addresses)) {
            return [];
        }

        return str($addresses)
            ->squish()
            ->explode(',')
            ->map(fn (string $address) => new Address($address))
            ->toArray();
    }
}
