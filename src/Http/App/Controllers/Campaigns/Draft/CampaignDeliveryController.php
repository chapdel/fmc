<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft;

use Spatie\Mailcoach\Models\Campaign;

class CampaignDeliveryController
{
    public function __invoke(Campaign $campaign)
    {
        return view('mailcoach::app.campaigns.draft.delivery', [
            'campaign' => $campaign,
        ]);
    }
}
