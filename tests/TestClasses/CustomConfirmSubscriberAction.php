<?php

namespace Spatie\Mailcoach\Tests\TestClasses;

use Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\ConfirmSubscriberAction;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;

class CustomConfirmSubscriberAction extends ConfirmSubscriberAction
{
    public function execute(Subscriber $subscriber): void
    {
        $subscriber->update(['email' => 'overridden@example.com']);

        parent::execute($subscriber);
    }
}
