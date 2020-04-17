<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns\Sent;

use Spatie\Mailcoach\Http\App\Queries\CampaignSendsQuery;
use Spatie\Mailcoach\Models\Campaign;

class OutboxController
{
    public function __invoke(Campaign $campaign)
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
