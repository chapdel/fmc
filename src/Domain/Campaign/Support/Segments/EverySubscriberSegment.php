<?php

namespace Spatie\Mailcoach\Domain\Campaign\Support\Segments;

class EverySubscriberSegment extends Segment
{
    public function description(): string
    {
        return (string)__('all subscribers');
    }
}
