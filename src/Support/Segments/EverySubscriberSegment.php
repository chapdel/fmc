<?php

namespace Spatie\Mailcoach\Support\Segments;

class EverySubscriberSegment extends Segment
{
    public function description(): string
    {
        return __('all subscribers');
    }
}
