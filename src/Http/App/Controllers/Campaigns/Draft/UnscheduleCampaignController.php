<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft;

use Spatie\Mailcoach\Models\Campaign;

class UnscheduleCampaignController
{
    public function __invoke(Campaign $campaign)
    {
        $campaign->markAsUnscheduled();

        flash()->success(__('Campaign :campaign was unscheduled', ['campaign' => $campaign->name]));

        return redirect()->route('mailcoach.campaigns.delivery', $campaign->id);
    }
}
