<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns\Sent;

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\App\Queries\CampaignOpensQuery;

class CampaignOpensController
{
    public function __invoke(Campaign $campaign)
    {
        $campaignOpensQuery = new CampaignOpensQuery($campaign);

        return view('mailcoach::app.campaigns.opens', [
            'campaign' => $campaign,
            'campaignOpens' => $campaignOpensQuery->paginate(),
            'totalCampaignOpensCount' => $campaignOpensQuery->totalCount,
        ]);
    }
}
