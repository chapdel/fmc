<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns\Sent;

use Spatie\Mailcoach\Http\App\Queries\CampaignOpensQuery;
use Spatie\Mailcoach\Models\Campaign;

class CampaignOpensController
{
    public function __invoke(Campaign $campaign)
    {
        $campaignOpensQuery = new CampaignOpensQuery($campaign);

        return view('mailcoach::app.campaigns.sent.opens', [
            'campaign' => $campaign,
            'campaignOpens' => $campaignOpensQuery->paginate(),
            'totalCampaignOpensCount' => $campaignOpensQuery->totalCount,
        ]);
    }
}
