<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns;

use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Models\Upload;

class DuplicateCampaignController
{
    public function __invoke(Campaign $campaign)
    {
        /** @var \Spatie\Mailcoach\Models\Campaign $duplicateCampaign */
        $duplicateCampaign = Campaign::create([
            'name' => "Duplicate of {$campaign->name}",
            'SUBJECT' => $campaign->subject,
            'email_list_id' => $campaign->email_list_id,
            'html' => $campaign->html,
            'structured_html' => $campaign->structured_html,
            'webview_html' => $campaign->webview_html,
            'track_opens' => $campaign->track_opens,
            'track_clicks' => $campaign->track_clicks,
            'last_modified_at' => now(),
            'segment_class' => $campaign->segment_class,
            'segment_id' => $campaign->segment_id,
        ]);

        $duplicateCampaign->update([
            'segment_description' => $duplicateCampaign->getSegment()->description($campaign),
        ]);

        $campaign->uploads->each(fn (Upload $upload) => $duplicateCampaign->uploads()->attach($upload));

        flash()->success("Campaign {$campaign->name} was duplicated.");

        return redirect()->route('mailcoach.campaigns.settings', $duplicateCampaign);
    }
}
