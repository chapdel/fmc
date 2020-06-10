<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft;

use Spatie\Mailcoach\Http\App\Requests\UpdateCampaignSettingsRequest;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Traits\UsesMailcoachModels;

class CampaignSettingsController
{
    use UsesMailcoachModels;

    public function edit(Campaign $campaign)
    {
        $emailLists = $this->getEmailListClass()::all();

        return view('mailcoach::app.campaigns.draft.settings', [
            'campaign' => $campaign,
            'emailLists' => $emailLists,
            'segmentsData' => $emailLists->map(function (EmailList $emailList) {
                return [
                    'id' => $emailList->id,
                    'name' => $emailList->name,
                    'segments' => $emailList->segments->map->only('id', 'name'),
                    'createSegmentUrl' => route('mailcoach.emailLists.segments', $emailList),
                ];
            }),
        ]);
    }

    public function update(Campaign $campaign, UpdateCampaignSettingsRequest $request)
    {
        $campaign->update([
            'name' => $request->name,
            'subject' => $request->subject,
            'email_list_id' => $request->email_list_id,
            'track_opens' => $request->track_opens ?? false,
            'track_clicks' => $request->track_clicks ?? false,
            'last_modified_at' => now(),
            'segment_class' => $request->getSegmentClass(),
            'segment_id' => $request->segment_id,
        ]);

        $campaign->update(['segment_description' => $campaign->getSegment()->description($campaign)]);

        flash()->success(__('Campaign :campaign was updated.', ['campaign' => $campaign->name]));

        return redirect()->route('mailcoach.campaigns.settings', $campaign->id);
    }
}
