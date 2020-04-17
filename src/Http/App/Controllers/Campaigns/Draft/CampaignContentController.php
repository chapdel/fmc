<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft;

use Spatie\Mailcoach\Http\App\Requests\UpdateCampaignContentRequest;
use Spatie\Mailcoach\Models\Concerns\Campaign as CampaignConcern;

class CampaignContentController
{
    public function edit(CampaignConcern $campaign)
    {
        return view('mailcoach::app.campaigns.draft.content', compact('campaign'));
    }

    public function update(CampaignConcern $campaign, UpdateCampaignContentRequest $request)
    {
        $campaign->update([
            'html' => $request->html,
            'structured_html' => $request->structured_html,
            'last_modified_at' => now(),
        ]);

        flash()->success("Campaign {$campaign->name} was updated.");

        return redirect()->route('mailcoach.campaigns.content', $campaign->id);
    }
}
