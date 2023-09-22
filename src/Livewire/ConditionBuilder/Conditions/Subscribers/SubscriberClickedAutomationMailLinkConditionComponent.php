<?php

namespace Spatie\Mailcoach\Livewire\ConditionBuilder\Conditions\Subscribers;

use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Livewire\ConditionBuilder\ConditionComponent;

class SubscriberClickedAutomationMailLinkConditionComponent extends ConditionComponent
{
    use UsesMailcoachModels;

    public ?int $automationMailId = null;

    public array $automationMails = [];

    public array $options = [];

    public function mount(): void
    {
        parent::mount();

        $this->changeLabels();

        $this->automationMails = self::getAutomationMailClass()::query()
            ->has('links')
            ->pluck('id', 'name')
            ->mapWithKeys(function (string $id, string $name) {
                return [$id => $name];
            })->toArray();
    }

    public function changeLabels(): void
    {
        foreach ($this->storedCondition['condition']['comparison_operators'] as $operator => $label) {
            $newLabel = match ($operator) {
                'any' => 'Clicked Any Link',
                'none' => 'Did Not Click Any Link',
                'equals' => 'Clicked A Specific Link',
                'not-equals' => 'Did Not Click A Specific Link',
            };

            $this->storedCondition['condition']['comparison_operators'][$operator] = $newLabel;
        }
    }

    public function render()
    {
        $this->options = self::getLinkClass()::query()
            ->where('automation_mail_id', $this->automationMailId)
            ->distinct()
            ->pluck('url')
            ->mapWithKeys(function (string $url) {
                return [$url => $url];
            })->toArray();

        return view('mailcoach::app.conditionBuilder.conditions.subscribers.subscriberClickedAutomationMailLinkCondition');
    }
}
