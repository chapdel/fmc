<?php

namespace Spatie\Mailcoach\Http\Front\Controllers;

use Spatie\Mailcoach\Traits\UsesMailcoachModels;

class CampaignWebviewController
{
    use UsesMailcoachModels;

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
