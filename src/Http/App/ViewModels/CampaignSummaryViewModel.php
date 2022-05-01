<?php

namespace Spatie\Mailcoach\Http\App\ViewModels;

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\ViewModels\ViewModel;

class CampaignSummaryViewModel extends ViewModel
{
    use UsesMailcoachModels;

    protected Campaign $campaign;

    public int $failedSendsCount = 0;

    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;

        $this->failedSendsCount = $this->campaign()->sends()->failed()->count();
    }

    public function campaign(): Campaign
    {
        return $this->campaign;
    }
}
