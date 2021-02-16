<?php

namespace Spatie\Mailcoach\Domain\Audience\Support\Segments;

use Spatie\Mailcoach\Domain\Audience\Support\Segments\Segment;

class EverySubscriberSegment extends Segment
{
    public function description(): string
    {
        return (string)__('all subscribers');
    }
}
