<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft;

use Spatie\Mailcoach\Http\App\Requests\ScheduleCampaignRequest;
use Spatie\Mailcoach\Models\Campaign;

class ScheduleCampaignController
{
    public function __invoke(Campaign $campaign, ScheduleCampaignRequest $request)
    {
        if (! $campaign->isPending()) {
            flash()->error("Campaign {$campaign->name} could not be scheduled because it has already been sent.");

            return back();
        }

        $campaign->scheduleToBeSentAt($request->getScheduledAt());

        flash()->success("Campaign {$campaign->name} is scheduled for delivery.");

        return redirect()->route('mailcoach.campaigns.delivery', $campaign->id);
    }
}
