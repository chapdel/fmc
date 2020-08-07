<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns\Sent;

use Spatie\Mailcoach\Http\App\Requests\UpdateCampaignSettingsRequest;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Traits\UsesMailcoachModels;

class CampaignUsedSettingsController
{
    public function __invoke(Campaign $campaign)
    {
        return view('mailcoach::app.campaigns.sent.settings', [
            'campaign' => $campaign,
        ]);
    }
}
