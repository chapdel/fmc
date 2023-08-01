<?php

namespace Spatie\Mailcoach\Livewire\ConditionBuilder\Conditions\Subscribers;

use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Livewire\ConditionBuilder\ConditionComponent;

class SubscriberOpenedAutomationMailConditionComponent extends ConditionComponent
{
    use UsesMailcoachModels;

    public array $options = [];

    public function mount(): void
    {
        parent::mount();

        $this->changeLabels();

        $this->options = self::getCampaignClass()::query()
            ->whereHas('opens', function ($query) {
                $query->where('subscriber_id', auth()->user()->id);
            })
            ->pluck('name')
            ->mapWithKeys(function (string $name) {
                return [$name => $name];
            })->toArray();
    }

    public function changeLabels(): void
    {
        foreach ($this->storedCondition['condition']['comparison_operators'] as $operator => $label) {
            $newLabel = match ($operator) {
                'any' => 'Opened Any',
                'none' => 'Did Not Open Any',
                'equals' => 'Opened',
                'not-equals' => 'Did Not Open',
            };

            $this->storedCondition['condition']['comparison_operators'][$operator] = $newLabel;
        }
    }

    public function render()
    {
        return view('mailcoach::app.conditionBuilder.conditions.subscribers.subscriberOpenedAutomationMailCondition');
    }
}
