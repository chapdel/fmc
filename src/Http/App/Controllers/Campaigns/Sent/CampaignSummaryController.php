<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns\Sent;

use Spatie\Mailcoach\Http\App\ViewModels\CampaignSummaryViewModel;
use Spatie\Mailcoach\Models\Campaign;

class CampaignSummaryController
{
    public function __invoke(Campaign $campaign)
    {
        $viewModel = new CampaignSummaryViewModel($campaign);

        return view('mailcoach::app.campaigns.sent.summary', $viewModel);
    }
}
