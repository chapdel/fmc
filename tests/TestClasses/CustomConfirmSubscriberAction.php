<?php

namespace Spatie\Mailcoach\Tests\TestClasses;

use Spatie\Mailcoach\Actions\Subscribers\ConfirmSubscriberAction;
use Spatie\Mailcoach\Models\Subscriber;

class CustomConfirmSubscriberAction extends ConfirmSubscriberAction
{
    public function execute(Subscriber $subscriber)
    {
        $subscriber->update(['email' => 'overridden@example.com']);

        parent::execute($subscriber);
    }
}
