<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft;

use Spatie\Mailcoach\Http\App\Requests\StoreCampaignRequest;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Support\Segments\EverySubscriberSegment;

class CreateCampaignController
{
    public function __invoke(StoreCampaignRequest $request)
    {
        $campaign = Campaign::create([
            'name' => $request->name,
            'subject' => $request->name,
            'html' => $request->template()->html,
            'track_opens' => true,
            'track_clicks' => true,
            'last_modified_at' => now(),
            'email_list_id' => optional(EmailList::orderBy('name')->first())->id,
            'segment_class' => EverySubscriberSegment::class,
        ]);

        $campaign->update(['segment_description' => (new EverySubscriberSegment())->description($campaign)]);

        flash()->success("Campaign {$campaign->name} was created.");

        return redirect()->route('mailcoach.campaigns.settings', $campaign);
    }
}
