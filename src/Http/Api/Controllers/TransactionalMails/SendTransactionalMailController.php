<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\TransactionalMails;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Mails\TransactionalMail;
use Spatie\Mailcoach\Http\Api\Controllers\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Http\Api\Requests\SendTransactionalMailRequest;
use Spatie\Mailcoach\Mailcoach;
use Symfony\Component\Mime\Address;

class SendTransactionalMailController
{
    use RespondsToApiRequests;
    use AuthorizesRequests;
    use UsesMailcoachModels;

    public function __invoke(SendTransactionalMailRequest $request)
    {
        $this->authorize('create', static::getSendClass());

        $mail = new TransactionalMail(
            mailName: $request->get('mail_name'),
            subject: $request->get('subject'),
            from: $request->get('from'),
            to: $this->normalizeEmailAddresses($request->get('to')),
            cc: $this->normalizeEmailAddresses($request->get('cc')),
            bcc: $this->normalizeEmailAddresses($request->get('bcc')),
            replyTo: $this->normalizeEmailAddresses($request->get('reply_to')),
            mailer: $request->get('mailer'),
            replacements: $request->replacements(),
            attachments: $request->attachments(),
            store: $request->shouldStoreMail(),
            html: $request->html,
        );

        Mail::mailer($request->get('mailer', Mailcoach::defaultTransactionalMailer()))->send($mail);

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
