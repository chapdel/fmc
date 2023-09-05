<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\TransactionalMails;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Shared\Actions\IsEmailOnSuppressionListAction;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Listeners\StoreTransactionalMail;
use Spatie\Mailcoach\Domain\TransactionalMail\Mails\TransactionalMail;
use Spatie\Mailcoach\Domain\TransactionalMail\Support\AddressNormalizer;
use Spatie\Mailcoach\Domain\TransactionalMail\Support\TransactionalMailMessageConfig;
use Spatie\Mailcoach\Http\Api\Controllers\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Http\Api\Requests\SendTransactionalMailRequest;
use Spatie\Mailcoach\Mailcoach;

class SendTransactionalMailController
{
    use AuthorizesRequests;
    use RespondsToApiRequests;
    use UsesMailcoachModels;

    public function __invoke(SendTransactionalMailRequest $request)
    {
        $this->authorize(
            'send',
            [static::getSendClass(), $request->getFromEmail(), $request->getToEmails()],
        );

        $normalizer = new AddressNormalizer();

        $transactionalMail = new TransactionalMail(
            mailName: $request->get('mail_name'),
            subject: $request->get('subject', ''),
            from: $normalizer->normalize($request->get('from')),
            to: $normalizer->normalize($request->get('to')),
            cc: $normalizer->normalize($request->get('cc')),
            bcc: $normalizer->normalize($request->get('bcc')),
            replyTo: $normalizer->normalize($request->get('reply_to')),
            mailer: $request->get('mailer'),
            replacements: $request->replacements(),
            attachments: $request->attachments(),
            store: $request->shouldStoreMail(),
            html: $request->html,
        );

        if ($request->fake) {
            $transactionalMail->build();
            $emailMock = $transactionalMail->toEmail();
            $emailMock->getHeaders()
                ->addTextHeader(TransactionalMailMessageConfig::HEADER_NAME_STORE, true)
                ->addTextHeader(TransactionalMailMessageConfig::HEADER_NAME_MAILABLE_CLASS, TransactionalMail::class);

            (new StoreTransactionalMail())
                ->handle(new MessageSending(($emailMock), ['fake' => true]));

            return $this->respondOk();
        }

        $action = Mailcoach::getSharedActionClass('is_on_suppression_list', IsEmailOnSuppressionListAction::class);

        // @todo what about cc & bcc?
        // @todo bulk check for less queries but disallowing all if one is invalid ?
        foreach ($transactionalMail->to as $key => $email) {
            if ($action->execute($email['address'])) {
                unset($transactionalMail->to[$key]);

                $transactionalMail->to = array_values($transactionalMail->to);
            }
        }

        if ($transactionalMail->to === []) {
            return $this->respondOk();
        }

        $name = $request->get('mailer', Mailcoach::defaultTransactionalMailer());

        Mail::mailer($name)->send($transactionalMail, ['fake' => $request->fake]);

        return $this->respondOk();
    }
}
