<?php

namespace Spatie\Mailcoach\Tests\TestClasses;

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\ActionSubscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class AllowDuplicateShouldAutomationRunForSubscriberAction
{
    use UsesMailcoachModels;

    public function execute(Automation $automation, Subscriber $subscriber): bool
    {
        if (! $subscriber->isSubscribed()) {
            return false;
        }

        if (! $automation
            ->newSubscribersQuery()
            ->where("{$this->getSubscriberTableName()}.id", $subscriber->id)
            ->exists()
        ) {
            return false;
        }

        return true;
    }
}
