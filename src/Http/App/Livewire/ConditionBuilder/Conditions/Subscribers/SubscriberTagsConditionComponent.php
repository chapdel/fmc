<?php

namespace Spatie\Mailcoach\Http\App\Livewire\ConditionBuilder\Conditions\Subscribers;

use Spatie\Mailcoach\Http\App\Livewire\ConditionBuilder\ConditionComponent;

class SubscriberTagsConditionComponent extends ConditionComponent
{
    public array $options = [];

    public function mount(): void
    {
        parent::mount();

        $this->options = self::getTagClass()::pluck('name', 'id')->toArray();
    }

    public function render()
    {
        return view('mailcoach::app.conditionBuilder.conditions.subscribers.subscriberTagsCondition');
    }
}
