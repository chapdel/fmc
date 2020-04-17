<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft;

use Spatie\Mailcoach\Models\Concerns\Campaign as CampaignConcern;

class SendCampaignController
{
    public function __invoke(CampaignConcern $campaign)
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
