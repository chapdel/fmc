<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Models\Template;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Queries\CampaignsQuery;

class CampaignsIndexController
{
    use AuthorizesRequests,
        UsesMailcoachModels;

    public function __invoke(CampaignsQuery $campaignsQuery)
    {
        $this->authorize("viewAny", Campaign::class);

        return view('mailcoach::app.campaigns.index', [
            'campaigns' => $campaignsQuery->paginate(),
            'campaignsQuery' => $campaignsQuery,
            'totalCampaignsCount' => $this->getCampaignClass()::count(),
            'totalListsCount' => $this->getEmailListClass()::count(),
            'sentCampaignsCount' => $this->getCampaignClass()::sendingOrSent()->count(),
            'scheduledCampaignsCount' => $this->getCampaignClass()::scheduled()->count(),
            'draftCampaignsCount' => $this->getCampaignClass()::draft()->count(),
            'templateOptions' => $this->getTemplateClass()::orderBy('name')->get()
                ->mapWithKeys(fn (Template $template) => [$template->id => $template->name])
                ->prepend('-- None --', 0),
            'emailListOptions' => $this->getEmailListClass()::orderBy('name')->get()
                ->mapWithKeys(fn (EmailList $list) => [$list->id => $list->name]),
        ]);
    }
}
