<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Livewire\Actions;

use Illuminate\Validation\Rule;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\AutomationActionComponent;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\AutomationComponent;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CampaignActionComponent extends AutomationActionComponent
{
    public int|string $campaign_id = '';

    public array $campaignOptions;

    public function mount() {
        parent::mount();

        $this->campaignOptions = Campaign::query()
            ->where('status', CampaignStatus::AUTOMATED)
            ->pluck('name', 'id')
            ->toArray();
    }

    public function getData(): array
    {
        return [
            'campaign_id' => $this->campaign_id,
        ];
    }

    public function rules(): array
    {
        return [
            'campaign_id' => ['required', Rule::exists(self::getCampaignTableName(), 'id')],
        ];
    }

    public function render()
    {
        return <<<'blade'
            <div>
                <x-mailcoach::select-field
                    label="Campaign"
                    name="campaign_id"
                    wire:model="campaign_id"
                    :placeholder="__('Select a campaign')"
                    :options="['' => 'Select a campaign'] + $campaignOptions"
                />
            </div>
        blade;
    }
}
