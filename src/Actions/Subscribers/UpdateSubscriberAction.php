<?php

namespace Spatie\Mailcoach\Actions\Subscribers;

use Spatie\Mailcoach\Models\Subscriber;

class UpdateSubscriberAction
{
    public function execute(Subscriber $subscriber, array $attributes, array $tags = []): void
    {
        $subscriber->fill($attributes);

        $subscriber->syncTags($tags);

        $subscriber->save();
    }
}
