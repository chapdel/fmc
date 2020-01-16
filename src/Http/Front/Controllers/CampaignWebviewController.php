<?php

namespace Spatie\Mailcoach\Http\Front\Controllers;

use Spatie\Mailcoach\Enums\CampaignStatus;
use Spatie\Mailcoach\Models\Campaign;

class CampaignWebviewController
{
    public function __invoke(string $campaignUuid)
    {
        if (! $campaign = Campaign::findByUuid($campaignUuid)) {
            abort(404);
        }

        if ($campaign->isDraft()) {
            abort(404);
        }

        return view('mailcoach::campaign.webview', compact('campaign'));
    }
}
