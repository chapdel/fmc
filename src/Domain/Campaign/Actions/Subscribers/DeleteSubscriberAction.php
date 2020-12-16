<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions\Subscribers;

use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;

class DeleteSubscriberAction
{
    public function execute(Subscriber $subscriber): void
    {
        $subscriber->tags()->detach();

        $subscriber->delete();
    }
}
