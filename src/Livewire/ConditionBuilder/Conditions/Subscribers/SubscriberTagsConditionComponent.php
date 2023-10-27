<?php

namespace Spatie\Mailcoach\Livewire\ConditionBuilder\Conditions\Subscribers;

use Spatie\Mailcoach\Livewire\ConditionBuilder\ConditionComponent;

class SubscriberTagsConditionComponent extends ConditionComponent
{
    public array $options = [];

    public function mount(): void
    {
        parent::mount();

        $this->options = self::getTagClass()::query()
            ->orderBy('type')
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    public function render()
    {
        return view('mailcoach::app.conditionBuilder.conditions.subscribers.subscriberTagsCondition');
    }
}
