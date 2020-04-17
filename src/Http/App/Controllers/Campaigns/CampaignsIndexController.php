<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns;

use Spatie\Mailcoach\Http\App\Queries\CampaignsQuery;
use Spatie\Mailcoach\Models\Template;
use Spatie\Mailcoach\Traits\UsesCampaign;
use Spatie\Mailcoach\Traits\UsesEmailList;

class CampaignsIndexController
{
    use UsesCampaign, UsesEmailList;

    public function __invoke(CampaignsQuery $campaignsQuery)
    {
        return view('mailcoach::app.campaigns.index', [
            'campaigns' => $campaignsQuery->paginate(),
            'campaignsQuery' => $campaignsQuery,
            'totalCampaignsCount' => $this->getCampaignClass()::count(),
            'totalListsCount' => $this->getEmailListClass()::count(),
            'sentCampaignsCount' => $this->getCampaignClass()::sendingOrSent()->count(),
            'scheduledCampaignsCount' => $this->getCampaignClass()::scheduled()->count(),
            'draftCampaignsCount' => $this->getCampaignClass()::draft()->count(),
            'templateOptions' => Template::orderBy('name')->get()
                ->mapWithKeys(fn (Template $template) => [$template->id => $template->name])
                ->prepend('-- None --', 0),
        ]);
    }
}
