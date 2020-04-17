<?php

namespace Spatie\Mailcoach\Traits;

use Spatie\Mailcoach\MailcoachRegistrar;

trait UsesSubscriber
{
    private $subscriberClass;

    public function getSubscriberClass()
    {
        if (! isset($this->subscriberClass)) {
            $this->subscriberClass = app(MailcoachRegistrar::class)->getSubscriberClass();
        }

        return $this->subscriberClass;
    }
}
