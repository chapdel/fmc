<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns\Sent;

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\App\ViewModels\CampaignSummaryViewModel;

class CampaignSummaryController
{
    public function __invoke(Campaign $campaign)
    {
        $viewModel = new CampaignSummaryViewModel($campaign);

        return view('mailcoach::app.campaigns.summary', $viewModel);
    }
}
