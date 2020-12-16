<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions\Subscribers;

use Spatie\Mailcoach\Domain\Campaign\Actions\Subscribers\Concerns\SendsWelcomeMail;
use Spatie\Mailcoach\Domain\Campaign\Events\SubscribedEvent;
use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;

class ConfirmSubscriberAction
{
    use SendsWelcomeMail;

    protected bool $sendWelcomeMail = true;

    public function doNotSendWelcomeMail(): self
    {
        $this->sendWelcomeMail = false;

        return $this;
    }

    public function execute(Subscriber $subscriber): void
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
