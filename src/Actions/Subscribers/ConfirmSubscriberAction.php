<?php

namespace Spatie\Mailcoach\Actions\Subscribers;

use Spatie\Mailcoach\Actions\Subscribers\Concerns\SendsWelcomeMail;
use Spatie\Mailcoach\Events\SubscribedEvent;
use Spatie\Mailcoach\Models\Subscriber;

class ConfirmSubscriberAction
{
    use SendsWelcomeMail;

    public function execute(Subscriber $subscriber)
    {
        $subscriber->update(['subscribed_at' => now()]);

        $this->sendWelcomeMail($subscriber);

        event(new SubscribedEvent($subscriber));
    }
}
