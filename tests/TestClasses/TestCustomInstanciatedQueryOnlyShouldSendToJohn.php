<?php

namespace Spatie\Mailcoach\Tests\TestClasses;

use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;
use Spatie\Mailcoach\Support\Segments\Segment;

class TestCustomInstanciatedQueryOnlyShouldSendToJohn extends Segment
{
    public string $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public function shouldSend(Subscriber $subscriber): bool
    {
        return $subscriber->email === $this->email;
    }

    public function description(): string
    {
        return 'only to john';
    }
}
