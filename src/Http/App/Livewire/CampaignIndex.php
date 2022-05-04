<?php

namespace Spatie\Mailcoach\Http\App\Livewire;

use Illuminate\Http\Request;
use Livewire\WithPagination;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Queries\CampaignsQuery;
use Spatie\Mailcoach\Http\App\Queries\Sorts\CampaignSort;

class CampaignIndex extends DataTable
{
    public string $sort = '-sent';

    public function deleteCampaign(int $id)
    {
        $campaign = Campaign::find($id);

        $this->authorize('delete', $campaign);

        $campaign->delete();

        $this->dispatchBrowserEvent('notify', [
            'content' => __('mailcoach - Campaign :campaign was deleted.', ['campaign' => $campaign->name]),
        ]);
    }

    public function render()
    {
        parent::render();

        return view('mailcoach::app.campaigns.index', [
            'campaigns' => (new CampaignsQuery(request()))->paginate(),
            'totalCampaignsCount' => Campaign::count(),
            'totalListsCount' => static::getEmailListClass()::count(),
            'sentCampaignsCount' => static::getCampaignClass()::sendingOrSent()->count(),
            'scheduledCampaignsCount' => static::getCampaignClass()::scheduled()->count(),
            'draftCampaignsCount' => static::getCampaignClass()::draft()->count(),
        ])->layout('mailcoach::app.layouts.main', [
            'title' => __('mailcoach - Campaigns'),
        ]);
    }
}
