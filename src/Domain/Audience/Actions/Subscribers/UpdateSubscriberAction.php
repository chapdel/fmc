<?php

namespace Spatie\Mailcoach\Domain\Audience\Actions\Subscribers;

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;

class UpdateSubscriberAction
{
    public function execute(Subscriber $subscriber, array $attributes, array $tags = []): void
    {
        $subscriber->fill($attributes);

        $subscriber->syncTags($tags);

        $subscriber->save();
    }
}
