<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\Automations;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Support\AutomationTriggers\WebhookTrigger;
use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class TriggerAutomationController
{
    use UsesMailcoachModels;

    public function __invoke(Request $request, Automation $automation)
    {
        $request->validate([
            'subscribers' => ['required', 'array'],
            'subscribers.*' => ['integer', Rule::exists($this->getSubscriberTableName(), 'id')],
        ]);

        abort_unless($automation->trigger instanceof WebhookTrigger, 400, "This automation does not have a Webhook trigger.");

        $automation->trigger->runAutomation(Subscriber::whereIn('id', $request->get('subscribers')));

        return response()->json();
    }
}
