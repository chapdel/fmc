<?php

namespace Spatie\Mailcoach\Http\App\Livewire\ConditionBuilder\Conditions\Subscribers;

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Http\App\Livewire\ConditionBuilder\ConditionComponent;

class SubscriberAttributesConditionComponent extends ConditionComponent
{
    public array $attributes = [];

    public array $options = [];

    public function mount(): void
    {
        parent::mount();

        $this->options = Subscriber::attributesFields();
    }

    public function render()
    {
        return view('mailcoach::app.conditionBuilder.conditions.subscribers.subscriberAttributesCondition');
    }
}
