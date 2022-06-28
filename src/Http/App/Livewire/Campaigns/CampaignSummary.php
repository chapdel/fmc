<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Campaigns;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;

class CampaignSummary extends Component
{
    use AuthorizesRequests;
    use LivewireFlash;

    public Campaign $campaign;

    public function mount(Campaign $campaign)
    {
        $this->campaign = $campaign;

        if ($campaign->status === CampaignStatus::Draft) {
            return redirect()->route('mailcoach.campaigns.delivery', $campaign);
        }
    }

    public function cancelSending()
    {
        $this->campaign->update([
            'status' => CampaignStatus::Cancelled,
            'sent_at' => now(),
        ]);

        $this->flash(__('mailcoach - Sending successfully cancelled.'));
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
