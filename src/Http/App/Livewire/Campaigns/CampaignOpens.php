<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Campaigns;

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\App\Livewire\DataTable;
use Spatie\Mailcoach\Http\App\Queries\CampaignOpensQuery;

class CampaignOpens extends DataTable
{
    public string $sort = '-first_opened_at';

    public Campaign $campaign;

    public function mount(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }

    public function getTitle(): string
    {
        return __('mailcoach - Opens');
    }

    public function getView(): string
    {
        return 'mailcoach::app.campaigns.opens';
    }

    public function getLayout(): string
    {
        return 'mailcoach::app.campaigns.layouts.campaign';
    }

    public function getLayoutData(): array
    {
        return [
            'campaign' => $this->campaign,
        ];
    }

    public function getData(): array
    {
        $campaignOpens = (new CampaignOpensQuery($this->campaign, request()));

        return [
            'campaign' => $this->campaign,
            'mailOpens' => $campaignOpens->paginate(),
            'totalMailOpensCount' => $campaignOpens->totalCount,
        ];
    }
}
