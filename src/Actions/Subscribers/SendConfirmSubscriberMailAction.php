<?php

namespace Spatie\Mailcoach\Actions\Subscribers;

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Events\UnconfirmedSubscriberCreatedEvent;
use Spatie\Mailcoach\Models\Subscriber;

class SendConfirmSubscriberMailAction
{
    public function execute(Subscriber $subscriber, string $redirectAfterSubscribed = '')
    {
        if (! $subscriber->isUnconfirmed()) {
            return;
        }

        $mailableClass = $subscriber->emailList->confirmSubscriberMailableClass();

        Mail::to($subscriber->email)->queue(new $mailableClass($subscriber, $redirectAfterSubscribed));

        event(new UnconfirmedSubscriberCreatedEvent($subscriber));
    }
}
