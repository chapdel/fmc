<?php

namespace Spatie\Mailcoach\Tests\TestClasses;

use Spatie\Mailcoach\Domain\Campaign\Actions\Subscribers\ConfirmSubscriberAction;
use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;

class CustomConfirmSubscriberAction extends ConfirmSubscriberAction
{
    public function execute(Subscriber $subscriber): void
    {
        $subscriber->update(['email' => 'overridden@example.com']);

        parent::execute($subscriber);
    }
}
