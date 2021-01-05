<?php

namespace Spatie\Mailcoach\Domain\Campaign\Events;

use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Models\Tag;

class TagRemovedEvent
{
    public function __construct(
        public Subscriber $subscriber,
        public Tag $tag,
    ) {}
}
