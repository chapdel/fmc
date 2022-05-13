<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Campaigns;

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\App\Livewire\DataTable;
use Spatie\Mailcoach\Http\App\Queries\CampaignUnsubscribesQuery;

class CampaignUnsubscribes extends DataTable
{
    public string $sort = '-created_at';

    public Campaign $campaign;

    public function mount(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }

    public function getTitle(): string
    {
        return __('mailcoach - Unsubscribes');
    }

    public function getView(): string
    {
        return 'mailcoach::app.campaigns.unsubscribes';
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
        $this->authorize('view', $this->campaign);

        return [
            'campaign' => $this->campaign,
            'unsubscribes' => (new CampaignUnsubscribesQuery($this->campaign, request()))->paginate(),
            'totalUnsubscribes' => $this->campaign->unsubscribes()->count(),
        ];
    }
}
