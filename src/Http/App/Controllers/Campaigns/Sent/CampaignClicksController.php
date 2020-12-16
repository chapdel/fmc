<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns\Sent;

use Spatie\Mailcoach\Http\App\Queries\CampaignLinksQuery;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;

class CampaignClicksController
{
    public function __invoke(Campaign $campaign)
    {
        $campaignLinksQuery = new CampaignLinksQuery($campaign);

        return view('mailcoach::app.campaigns.clicks', [
            'campaign' => $campaign,
            'links' => $campaignLinksQuery->paginate(),
            'totalLinksCount' => $campaign->links()->count(),
        ]);
    }
}
