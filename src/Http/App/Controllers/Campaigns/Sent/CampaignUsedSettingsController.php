<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns\Sent;

use Spatie\Mailcoach\Models\Campaign;

class CampaignUsedSettingsController
{
    public function __invoke(Campaign $campaign)
    {
        return view('mailcoach::app.campaigns.sent.settings', [
            'campaign' => $campaign,
        ]);
    }
}
