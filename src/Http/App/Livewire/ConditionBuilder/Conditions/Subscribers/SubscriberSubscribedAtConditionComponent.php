<?php

namespace Spatie\Mailcoach\Http\App\Livewire\ConditionBuilder\Conditions\Subscribers;

use Spatie\Mailcoach\Http\App\Livewire\ConditionBuilder\ConditionComponent;

class SubscriberSubscribedAtConditionComponent extends ConditionComponent
{
    public function render()
    {
        return view('mailcoach::app.conditionBuilder.conditions.subscribers.subscriberSubscribedAtCondition');
    }
}
