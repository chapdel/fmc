<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft;

use Spatie\Mailcoach\Models\Concerns\Campaign as CampaignConcern;

class UnscheduleCampaignController
{
    public function __invoke(CampaignConcern $campaign)
    {
        $campaign->markAsUnscheduled();

        flash()->success("Campaign {$campaign->name} was unscheduled.");

        return redirect()->route('mailcoach.campaigns.delivery', $campaign->id);
    }
}
