<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Campaigns;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;

class CampaignSummary extends Component
{
    use AuthorizesRequests;

    public Campaign $campaign;

    public function mount(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }

    public function render()
    {
        $this->authorize('view', $this->campaign);

        return view('mailcoach::app.campaigns.summary', [
            'failedSendsCount' => $this->campaign->sends()->failed()->count(),
        ])->layout('mailcoach::app.campaigns.layouts.campaign', [
            'campaign' => $this->campaign,
        ]);
    }
}
