<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Campaigns;

use Spatie\Mailcoach\Http\App\Livewire\DataTable;
use Spatie\Mailcoach\Http\App\Queries\CampaignsQuery;

class CampaignIndex extends DataTable
{
    public string $sort = '-sent';

    public function deleteCampaign(int $id)
    {
        $campaign = self::getCampaignClass()::find($id);

        $this->authorize('delete', $campaign);

        $campaign->delete();

        $this->dispatchBrowserEvent('notify', [
            'content' => __('mailcoach - Campaign :campaign was deleted.', ['campaign' => $campaign->name]),
        ]);
    }

    public function getTitle(): string
    {
        return __('mailcoach - Campaigns');
    }

    public function getView(): string
    {
        return 'mailcoach::app.campaigns.index';
    }

    public function getData(): array
    {
        return [
            'campaigns' => (new CampaignsQuery(request()))->paginate(),
            'totalCampaignsCount' => self::getCampaignClass()::count(),
            'totalListsCount' => static::getEmailListClass()::count(),
            'sentCampaignsCount' => static::getCampaignClass()::sendingOrSent()->count(),
            'scheduledCampaignsCount' => static::getCampaignClass()::scheduled()->count(),
            'draftCampaignsCount' => static::getCampaignClass()::draft()->count(),
        ];
    }
}
