<?php

namespace Spatie\Mailcoach\Support\Segments;

class EverySubscriberSegment extends Segment
{
    public function description(): string
    {
        return (string)__('all subscribers');
    }
}
