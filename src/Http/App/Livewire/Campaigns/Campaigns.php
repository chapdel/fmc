<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Campaigns;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Http\App\Livewire\DataTable;
use Spatie\Mailcoach\Http\App\Queries\CampaignsQuery;

class Campaigns extends DataTable
{
    public string $sort = '-sent';

    public array $allowedFilters = [
        'status' => ['except' => '']
    ];

    public function mount()
    {
        $this->authorize("viewAny", static::getCampaignClass());
    }

    public function duplicateCampaign(int $id)
    {
        $this->authorize('create', self::getCampaignClass());

        $campaign = self::getCampaignClass()::find($id);

        /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign $duplicateCampaign */
        $duplicateCampaign = self::getCampaignClass()::create([
            'name' => __('mailcoach - Duplicate of') . ' ' . $campaign->name,
            'subject' => $campaign->subject,
            'email_list_id' => $campaign->email_list_id,
            'html' => $campaign->html,
            'structured_html' => $campaign->structured_html,
            'webview_html' => $campaign->webview_html,
            'track_opens' => $campaign->track_opens,
            'track_clicks' => $campaign->track_clicks,
            'utm_tags' => $campaign->utm_tags,
            'last_modified_at' => now(),
            'segment_class' => $campaign->segment_class,
            'segment_id' => $campaign->segment_id,
        ]);

        $duplicateCampaign->update([
            'segment_description' => $duplicateCampaign->getSegment()->description(),
        ]);

        flash()->success(__('mailcoach - Campaign :campaign was duplicated.', ['campaign' => $campaign->name]));

        return redirect()->route('mailcoach.campaigns.settings', $duplicateCampaign);
    }

    public function deleteCampaign(int $id)
    {
        $campaign = self::getCampaignClass()::find($id);

        $this->authorize('delete', $campaign);

        $campaign->delete();

        $this->flash(__('mailcoach - Campaign :campaign was deleted.', ['campaign' => $campaign->name]));
    }

    public function getTitle(): string
    {
        return __('mailcoach - Campaigns');
    }

    public function getView(): string
    {
        return 'mailcoach::app.campaigns.index';
    }

    public function getData(Request $request): array
    {
        return [
            'campaigns' => (new CampaignsQuery($request))->paginate(),
            'totalCampaignsCount' => self::getCampaignClass()::count(),
            'totalListsCount' => static::getEmailListClass()::count(),
            'sentCampaignsCount' => static::getCampaignClass()::sendingOrSent()->count(),
            'scheduledCampaignsCount' => static::getCampaignClass()::scheduled()->count(),
            'draftCampaignsCount' => static::getCampaignClass()::draft()->count(),
        ];
    }
}
