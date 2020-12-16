<?php

namespace Spatie\Mailcoach\Tests\TestClasses;

use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Support\Segments\Segment;

class TestCustomQueryOnlyShouldSendToJohn extends Segment
{
    public function shouldSend(Subscriber $subscriber): bool
    {
        return $subscriber->email === 'john@example.com';
    }

    public function description(): string
    {
        return 'only to john';
    }
}
