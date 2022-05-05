<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Campaigns;

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\App\Livewire\DataTable;
use Spatie\Mailcoach\Http\App\Queries\CampaignOpensQuery;
use Spatie\Mailcoach\Http\App\Queries\CampaignSendsQuery;

class CampaignOutbox extends DataTable
{
    public string $sort = '-sent_at';

    public Campaign $campaign;

    public function mount(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }

    public function getTitle(): string
    {
        return __('mailcoach - Outbox');
    }

    public function getView(): string
    {
        return 'mailcoach::app.campaigns.outbox';
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

        $sendsQuery = (new CampaignSendsQuery($this->campaign, request()));

        return [
            'campaign' => $this->campaign,
            'sends' => $sendsQuery->paginate(),
            'totalSends' => $this->campaign->sends()->count(),
            'totalPending' => $this->campaign->sends()->pending()->count(),
            'totalSent' => $this->campaign->sends()->sent()->count(),
            'totalFailed' => $this->campaign->sends()->failed()->count(),
            'totalBounces' => $this->campaign->sends()->bounced()->count(),
            'totalComplaints' => $this->campaign->sends()->complained()->count(),
        ];
    }
}
