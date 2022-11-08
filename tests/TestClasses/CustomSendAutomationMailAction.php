<?php

namespace Spatie\Mailcoach\Tests\TestClasses;

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\ActionSubscriber;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\SendAutomationMailAction;

class CustomSendAutomationMailAction extends SendAutomationMailAction
{
    public function run(Subscriber $subscriber, ?ActionSubscriber $actionSubscriber = null): void
    {
        if (isset($actionSubscriber)) {
            throw new \Exception('ActionSubscriber is set!');
        }

        parent::run($subscriber);
    }
}
