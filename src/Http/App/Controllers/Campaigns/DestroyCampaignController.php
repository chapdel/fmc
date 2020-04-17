<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns;

use Spatie\Mailcoach\Models\Campaign;

class DestroyCampaignController
{
    public function __invoke(Campaign $campaign)
    {
        $campaign->delete();

        flash()->success("Campaign {$campaign->name} was deleted.");

        return redirect()->back();
    }
}
