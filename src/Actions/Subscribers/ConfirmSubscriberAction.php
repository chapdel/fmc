<?php

namespace Spatie\Mailcoach\Actions\Subscribers;

use Spatie\Mailcoach\Actions\Subscribers\Concerns\SendsWelcomeMail;
use Spatie\Mailcoach\Events\SubscribedEvent;
use Spatie\Mailcoach\Models\Subscriber;

class ConfirmSubscriberAction
{
    use SendsWelcomeMail;

    protected bool $sendWelcomeMail = true;

    public function doNotSendWelcomeMail(): self
    {
        $this->sendWelcomeMail = false;

        return $this;
    }

    public function execute(Subscriber $subscriber)
    {
        $subscriber->update([
            'subscribed_at' => now(),
        ]);

        if ($this->sendWelcomeMail) {
            $this->sendWelcomeMail($subscriber);
        }

        event(new SubscribedEvent($subscriber));
    }
}
