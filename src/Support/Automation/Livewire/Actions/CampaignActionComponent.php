<?php

namespace Spatie\Mailcoach\Support\Automation\Livewire\Actions;

use Illuminate\Validation\Rule;
use Spatie\Mailcoach\Enums\CampaignStatus;
use Spatie\Mailcoach\Support\Automation\Livewire\AutomationComponent;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Traits\UsesMailcoachModels;

class CampaignActionComponent extends AutomationComponent
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
