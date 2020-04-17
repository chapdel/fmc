<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft;

use Spatie\Mailcoach\Models\Concerns\Campaign as CampaignConcern;

class CampaignDeliveryController
{
    public function __invoke(CampaignConcern $campaign)
    {
        return view('mailcoach::app.campaigns.draft.delivery', [
            'campaign' => $campaign,
        ]);
    }
}
