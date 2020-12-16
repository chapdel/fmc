<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions\Subscribers;

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Campaign\Events\UnconfirmedSubscriberCreatedEvent;
use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;

class SendConfirmSubscriberMailAction
{
    public function execute(Subscriber $subscriber, string $redirectAfterSubscribed = ''): void
    {
        if (! $subscriber->isUnconfirmed()) {
            return;
        }

        $mailableClass = $subscriber->emailList->confirmSubscriberMailableClass();

        Mail::mailer($subscriber->emailList->transactional_mailer)
            ->to($subscriber->email)
            ->queue(new $mailableClass($subscriber, $redirectAfterSubscribed));

        event(new UnconfirmedSubscriberCreatedEvent($subscriber));
    }
}
