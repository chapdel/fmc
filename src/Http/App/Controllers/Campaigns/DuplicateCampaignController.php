<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns;

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Support\Traits\UsesMailcoachModels;

class DuplicateCampaignController
{
    use UsesMailcoachModels;

    public function __invoke(Campaign $campaign)
    {
        /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign $duplicateCampaign */
        $duplicateCampaign = $this->getCampaignClass()::create([
            'name' => __('Duplicate of') . ' ' . $campaign->name,
            'subject' => $campaign->subject,
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
            'segment_description' => $duplicateCampaign->getSegment()->description(),
        ]);

        flash()->success(__('Campaign :campaign was duplicated.', ['campaign' => $campaign->name]));

        return redirect()->route('mailcoach.campaigns.settings', $duplicateCampaign);
    }
}
