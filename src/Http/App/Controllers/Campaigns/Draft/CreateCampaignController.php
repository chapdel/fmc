<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft;

use Spatie\Mailcoach\Http\App\Requests\StoreCampaignRequest;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Support\Segments\EverySubscriberSegment;
use Spatie\Mailcoach\Traits\UsesMailcoachModels;

class CreateCampaignController
{
    use UsesMailcoachModels;

    public function __invoke(StoreCampaignRequest $request)
    {
        /** @var Campaign $campaign */
        $campaign = $this->getCampaignClass()::create([
            'name' => $request->name,
            'subject' => $request->name,
            'html' => $request->template()->html,
            'structured_html' => $request->template()->structured_html,
            'track_opens' => true,
            'track_clicks' => true,
            'last_modified_at' => now(),
            'email_list_id' => $request->email_list_id ?? optional($this->getEmailListClass()::orderBy('name')->first())->id,
            'segment_class' => EverySubscriberSegment::class,
        ]);

        $campaign->update(['segment_description' => (new EverySubscriberSegment())->description($campaign)]);

        flash()->success(__('Campaign :campaign was created.', ['campaign' => $campaign->name]));

        return redirect()->route('mailcoach.campaigns.settings', $campaign);
    }
}
