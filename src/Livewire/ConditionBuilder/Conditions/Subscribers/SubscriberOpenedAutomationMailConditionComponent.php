<?php

namespace Spatie\Mailcoach\Livewire\ConditionBuilder\Conditions\Subscribers;

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Content\Models\Open;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Livewire\ConditionBuilder\ConditionComponent;

class SubscriberOpenedAutomationMailConditionComponent extends ConditionComponent
{
    use UsesMailcoachModels;

    public array $options = [];

    public EmailList $emailList;

    public function mount(): void
    {
        parent::mount();

        $this->changeLabels();

        $this->options = self::getOpenClass()::query()
            ->where('email_list_id', $this->emailList->id)
            ->with('contentItem.model')
            ->whereHas('contentItem', function ($query) {
                $query->where('model_type', (new (self::getAutomationMailClass()))->getMorphClass());
            })
            ->get()
            ->mapWithKeys(function (Open $open) {
                return [$open->contentItem->model->id => $open->contentItem->model->name];
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
