<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns;

use Spatie\Mailcoach\Models\Campaign;

class DestroyCampaignController
{
    public function __invoke(Campaign $campaign)
    {
        $campaign->delete();

        flash()->success(__('Campaign :campaign was deleted.', ['campaign' => $campaign->name]));

        return redirect()->back();
    }
}
