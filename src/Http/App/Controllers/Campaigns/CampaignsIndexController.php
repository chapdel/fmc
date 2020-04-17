<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns;

use Spatie\Mailcoach\Http\App\Queries\CampaignsQuery;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Models\Template;
use Spatie\Mailcoach\Traits\UsesCampaign;

class CampaignsIndexController
{
    use UsesCampaign;

    public function __invoke(CampaignsQuery $campaignsQuery)
    {
        return view('mailcoach::app.campaigns.index', [
            'campaigns' => $campaignsQuery->paginate(),
            'campaignsQuery' => $campaignsQuery,
            'totalCampaignsCount' => $this->getCampaignClass()::count(),
            'totalListsCount' => EmailList::count(),
            'sentCampaignsCount' => $this->getCampaignClass()::sendingOrSent()->count(),
            'scheduledCampaignsCount' => $this->getCampaignClass()::scheduled()->count(),
            'draftCampaignsCount' => $this->getCampaignClass()::draft()->count(),
            'templateOptions' => Template::orderBy('name')->get()
                ->mapWithKeys(fn (Template $template) => [$template->id => $template->name])
                ->prepend('-- None --', 0),
        ]);
    }
}
