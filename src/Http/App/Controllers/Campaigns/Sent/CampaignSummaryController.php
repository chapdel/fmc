<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns\Sent;

use Spatie\Mailcoach\Http\App\ViewModels\CampaignSummaryViewModel;
use Spatie\Mailcoach\Models\Concerns\Campaign as CampaignConcern;

class CampaignSummaryController
{
    public function __invoke(CampaignConcern $campaign)
    {
        $viewModel = new CampaignSummaryViewModel($campaign);

        return view('mailcoach::app.campaigns.sent.summary', $viewModel);
    }
}
