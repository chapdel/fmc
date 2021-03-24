<?php

namespace Spatie\Mailcoach\Tests\TestClasses;

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Audience\Support\Segments\Segment;

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
