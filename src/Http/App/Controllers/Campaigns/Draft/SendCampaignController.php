<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft;

use Spatie\Mailcoach\Models\Campaign;

class SendCampaignController
{
    public function __invoke(Campaign $campaign)
    {
        if (! $campaign->isPending()) {
            flash()->error(__('Campaign :campaign could not be sent because it has already been sent.', ['campaign' => $campaign->name]));

            return back();
        }

        $campaign->send();

        flash()->success(__('Campaign :campaign is being sent.', ['campaign' => $campaign->name]));

        return redirect()->route('mailcoach.campaigns.summary', $campaign->id);
    }
}
