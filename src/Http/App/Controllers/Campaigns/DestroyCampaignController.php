<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns;

use Spatie\Mailcoach\Models\Concerns\Campaign as CampaignConcern;

class DestroyCampaignController
{
    public function __invoke(CampaignConcern $campaign)
    {
        $campaign->delete();

        flash()->success("Campaign {$campaign->name} was deleted.");

        return redirect()->back();
    }
}
