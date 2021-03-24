<?php

namespace Spatie\Mailcoach\Tests\TestClasses;

use Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\CreateSubscriberAction;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Audience\Support\PendingSubscriber;

class CustomCreateSubscriberAction extends CreateSubscriberAction
{
    public function execute(PendingSubscriber $pendingSubscriber): Subscriber
    {
        $pendingSubscriber->email = 'overridden@example.com';

        return parent::execute($pendingSubscriber);
    }
}
