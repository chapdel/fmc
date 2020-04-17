<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns;

use Spatie\Mailcoach\Http\App\Queries\CampaignsQuery;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Models\Template;

class CampaignsIndexController
{
    public function __invoke(CampaignsQuery $campaignsQuery)
    {
        return view('mailcoach::app.campaigns.index', [
            'campaigns' => $campaignsQuery->paginate(),
            'campaignsQuery' => $campaignsQuery,
            'totalCampaignsCount' => Campaign::count(),
            'totalListsCount' => EmailList::count(),
            'sentCampaignsCount' => Campaign::sendingOrSent()->count(),
            'scheduledCampaignsCount' => Campaign::scheduled()->count(),
            'draftCampaignsCount' => Campaign::draft()->count(),
            'templateOptions' => Template::orderBy('name')->get()
                ->mapWithKeys(fn (Template $template) => [$template->id => $template->name])
                ->prepend('-- None --', 0),
        ]);
    }
}
