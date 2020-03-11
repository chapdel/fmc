<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft;

use Spatie\Mailcoach\Models\Campaign;

class SendCampaignController
{
    public function __invoke(Campaign $campaign)
    {
        if (! $campaign->isPending()) {
            flash()->error("Campaign {$campaign->name} could not be sent because it has already been sent.");

            return back();
        }

        $campaign->send();

        flash()->success("Campaign {$campaign->name} is being sent.");

        return redirect()->route('mailcoach.campaigns.summary', $campaign->id);
    }
}
