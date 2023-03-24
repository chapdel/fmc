<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Campaigns;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Models\CampaignLink;
use Spatie\Mailcoach\Http\App\Livewire\DataTableComponent;
use Spatie\Mailcoach\Http\App\Queries\CampaignLinkClicksQuery;
use Spatie\Mailcoach\Http\App\Queries\CampaignLinksQuery;
use Spatie\Mailcoach\MainNavigation;

class CampaignLinkClicksComponent extends DataTableComponent
{
    public string $sort = '-first_clicked_at';

    public CampaignLink $campaignLink;

    public function mount(CampaignLink $campaignLink)
    {
        $this->campaignLink = $campaignLink;

        app(MainNavigation::class)->activeSection()
            ?->add($this->campaignLink->campaign->name, route('mailcoach.campaigns'))
            ?->add('Clicks', route('mailcoach.campaigns.clicks', $this->campaignLink->campaign));
    }

    public function getTitle(): string
    {
        return $this->campaignLink->url . ' ' . __mc('Clicks');
    }

    public function getView(): string
    {
        return 'mailcoach::app.campaigns.linkClicks';
    }

    public function getLayout(): string
    {
        return 'mailcoach::app.campaigns.layouts.campaign';
    }

    public function getLayoutData(): array
    {
        return [
            'campaign' => $this->campaignLink->campaign,
        ];
    }

    public function getData(Request $request): array
    {
        $campaignLinkClicks = (new CampaignLinkClicksQuery($this->campaignLink, $request))->paginate($request->per_page);

        return [
            'campaignLink' => $this->campaignLink,
            'linkClicks' => $campaignLinkClicks,
            'totalLinkClicksCount' => $campaignLinkClicks->total(),
        ];
    }
}
