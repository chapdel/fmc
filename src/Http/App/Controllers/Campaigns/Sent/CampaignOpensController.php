<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns\Sent;

use Spatie\Mailcoach\Http\App\Queries\CampaignOpensQuery;
use Spatie\Mailcoach\Models\Concerns\Campaign as CampaignConcern;

class CampaignOpensController
{
    public function __invoke(CampaignConcern $campaign)
    {
        $campaignOpensQuery = new CampaignOpensQuery($campaign);

        return view('mailcoach::app.campaigns.sent.opens', [
            'campaign' => $campaign,
            'campaignOpens' => $campaignOpensQuery->paginate(),
            'totalCampaignOpensCount' => $campaignOpensQuery->totalCount,
        ]);
    }
}
