<?php

namespace Spatie\Mailcoach\Http\Front\Controllers;

use Spatie\Mailcoach\Traits\UsesCampaign;

class CampaignWebviewController
{
    use UsesCampaign;

    public function __invoke(string $campaignUuid)
    {
        if (! $campaign = $this->getCampaignClass()::findByUuid($campaignUuid)) {
            abort(404);
        }

        if ($campaign->isDraft()) {
            abort(404);
        }

        return view('mailcoach::campaign.webview', compact('campaign'));
    }
}
