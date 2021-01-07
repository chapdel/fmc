<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\Automations;

use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\WebhookTrigger;
use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;

class TriggerAutomationController
{
    public function __invoke(Automation $automation, Subscriber $subscriber)
    {
        abort_unless($automation->trigger instanceof WebhookTrigger, 400, "This automation does not have a Webhook trigger.");
        abort_unless($subscriber->emailList->is($automation->emailList), 401, "This subscriber does not belong to the automation email list.");
        abort_unless($subscriber->isSubscribed(), 401, "This subscriber is not subscribed.");

        $automation->trigger->fire($subscriber);

        return response()->json();
    }
}
