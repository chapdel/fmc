<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns\Sent;

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\App\Queries\CampaignUnsubscribesQuery;

class CampaignUnsubscribesController
{
    public function __invoke(Campaign $campaign)
    {
        return view('mailcoach::app.campaigns.unsubscribes', [
            'campaign' => $campaign,
            'unsubscribes' => (new CampaignUnsubscribesQuery($campaign))->paginate(),
            'totalUnsubscribes' => $campaign->unsubscribes()->count(),
        ]);
    }
}
