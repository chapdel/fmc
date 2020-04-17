<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns\Sent;

use Spatie\Mailcoach\Http\App\Queries\CampaignUnsubscribesQuery;
use Spatie\Mailcoach\Models\Concerns\Campaign as CampaignConcern;

class CampaignUnsubscribesController
{
    public function __invoke(CampaignConcern $campaign)
    {
        return view('mailcoach::app.campaigns.sent.unsubscribes', [
            'campaign' => $campaign,
            'unsubscribes' => (new CampaignUnsubscribesQuery($campaign))->paginate(),
            'totalUnsubscribes' => $campaign->unsubscribes()->count(),
        ]);
    }
}
