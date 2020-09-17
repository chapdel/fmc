<?php

namespace Spatie\Mailcoach\Actions\Subscribers;

use Spatie\Mailcoach\Models\Subscriber;

class DeleteSubscriberAction
{
    public function execute(Subscriber $subscriber): void
    {
        $subscriber->tags()->detach();

        $subscriber->delete();
    }
}
