<?php

namespace Spatie\Mailcoach\Tests\TestClasses;

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Support\Conditions\Condition;

class CustomCondition implements Condition
{
    public function __construct(private Subscriber $subscriber, private array $data) {}

    public static function getName(): string
    {
        return 'A custom condition';
    }

    public static function getDescription(array $data): string
    {
        return 'Some description';
    }

    public function check(): bool
    {
        return true;
    }
}
