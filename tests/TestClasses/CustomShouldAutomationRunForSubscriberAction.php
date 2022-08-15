<?php

namespace Spatie\Mailcoach\Tests\TestClasses;

use Exception;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;

class CustomShouldAutomationRunForSubscriberAction
{
    public function execute(Automation $automation, Subscriber $subscriber): bool
    {
        throw new Exception('CustomShouldAutomationRunForSubscriberAction was used');
    }
}
