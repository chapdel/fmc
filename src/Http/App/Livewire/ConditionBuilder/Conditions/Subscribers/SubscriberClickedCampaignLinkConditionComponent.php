<?php

namespace Spatie\Mailcoach\Http\App\Livewire\ConditionBuilder\Conditions\Subscribers;

use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Livewire\ConditionBuilder\ConditionComponent;

class SubscriberClickedCampaignLinkConditionComponent extends ConditionComponent
{
    use UsesMailcoachModels;

    public ?int $campaignId = null;

    public array $campaigns = [];

    public array $options = [];

    public array $comparisonOptions;

    public function mount(): void
    {
        parent::mount();

        $this->changeLabels();

        $this->campaigns = self::getCampaignClass()::query()
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
        $this->options = self::getCampaignLinkClass()::query()
            ->where('campaign_id', $this->campaignId)
            ->distinct()
            ->pluck('url')
            ->mapWithKeys(function (string $url) {
                return [$url => $url];
            })->toArray();

        return view('mailcoach::app.conditionBuilder.conditions.subscribers.subscriberClickedCampaignLinkCondition');
    }
}
