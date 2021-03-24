<?php

namespace Spatie\Mailcoach\Tests\TestClasses;

use Spatie\Mailcoach\Domain\Audience\Support\Segments\Segment;

class TestSegmentAllSubscribers extends Segment
{
    public function description(): string
    {
        return 'all subscribers';
    }
}
