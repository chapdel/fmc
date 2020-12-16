<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft;

use Spatie\Mailcoach\Http\App\Requests\Campaigns\UpdateCampaignContentRequest;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;

class CampaignContentController
{
    public function edit(Campaign $campaign)
    {
        return view('mailcoach::app.campaigns.content', compact('campaign'));
    }

    public function update(Campaign $campaign, UpdateCampaignContentRequest $request)
    {
        $campaign->update([
            'html' => $request->html,
            'structured_html' => $request->structured_html,
            'last_modified_at' => now(),
        ]);

        flash()->success(__('Campaign :campaign was updated.', ['campaign' => $campaign->name]));

        return redirect()->route('mailcoach.campaigns.content', $campaign->id);
    }
}
