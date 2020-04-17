<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns\Sent;

use Spatie\Mailcoach\Http\App\Queries\CampaignLinksQuery;
use Spatie\Mailcoach\Models\Concerns\Campaign as CampaignConcern;

class CampaignClicksController
{
    public function __invoke(CampaignConcern $campaign)
    {
        $campaignLinksQuery = new CampaignLinksQuery($campaign);

        return view('mailcoach::app.campaigns.sent.clicks', [
            'campaign' => $campaign,
            'links' => $campaignLinksQuery->paginate(),
            'totalLinksCount' => $campaign->links()->count(),
        ]);
    }
}
