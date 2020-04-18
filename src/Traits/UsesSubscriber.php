<?php

namespace Spatie\Mailcoach\Traits;

trait UsesSubscriber
{
    private string $subscriberClass;

    public function getSubscriberClass(): string
    {
        return config('mailcoach.models.campaign');
    }
}
