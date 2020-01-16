<?php

namespace Spatie\Mailcoach\Tests\TestClasses;

use Spatie\Mailcoach\Actions\Subscribers\CreateSubscriberAction;
use Spatie\Mailcoach\Models\Subscriber;
use Spatie\Mailcoach\Support\PendingSubscriber;

class CustomCreateSubscriberAction extends CreateSubscriberAction
{
    public function execute(PendingSubscriber $pendingSubscriber): Subscriber
    {
        $pendingSubscriber->email = 'overridden@example.com';

        return parent::execute($pendingSubscriber);
    }
}
