<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns\Sent;

use Spatie\Mailcoach\Http\App\Queries\CampaignSendsQuery;
use Spatie\Mailcoach\Models\Concerns\Campaign as CampaignConcern;

class OutboxController
{
    public function __invoke(CampaignConcern $campaign)
    {
        $sendsQuery = new CampaignSendsQuery($campaign);

        return view('mailcoach::app.campaigns.sent.outbox', [
            'campaign' => $campaign,
            'sends' => $sendsQuery->paginate(),
            'totalSends' => $campaign->sends()->count(),
            'totalPending' => $campaign->sends()->pending()->count(),
            'totalSent' => $campaign->sends()->sent()->count(),
            'totalFailed' => $campaign->sends()->failed()->count(),
            'totalBounces' => $campaign->sends()->bounced()->count(),
            'totalComplaints' => $campaign->sends()->complained()->count(),
        ]);
    }
}
