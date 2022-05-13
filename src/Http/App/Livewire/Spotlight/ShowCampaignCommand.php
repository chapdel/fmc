<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Spotlight;

use Illuminate\Http\Request;
use LivewireUI\Spotlight\Spotlight;
use LivewireUI\Spotlight\SpotlightCommand;
use LivewireUI\Spotlight\SpotlightCommandDependencies;
use LivewireUI\Spotlight\SpotlightCommandDependency;
use LivewireUI\Spotlight\SpotlightSearchResult;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Actions\UpdateCampaignAction;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Queries\Filters\FuzzyFilter;

class ShowCampaignCommand extends SpotlightCommand
{
    use UsesMailcoachModels;

    public function getName(): string
    {
        return __('mailcoach - Show :resource', ['resource' => 'campaign']);
    }

    public function getSynonyms(): array
    {
        return [
            __('mailcoach - View :resource', ['resource' => 'campaign']),
            __('mailcoach - Go :resource', ['resource' => 'campaign']),
        ];
    }

    public function dependencies(): ?SpotlightCommandDependencies
    {
        return SpotlightCommandDependencies::collection()
            ->add(SpotlightCommandDependency::make('campaign')->setPlaceholder('Campaign')->setType(SpotlightCommandDependency::SEARCH));
    }

    public function searchCampaign($query)
    {
        return self::getCampaignClass()::where('name', 'like', "%$query%")
            ->get()
            ->map(function(Campaign $campaign) {
                return new SpotlightSearchResult(
                    $campaign->id,
                    $campaign->name,
                    "{$campaign->emailList->name} - {$campaign->status}"
                );
            });
    }

    public function shouldBeShown(Request $request): bool
    {
        return $request->user()->can('view', self::getCampaignClass());
    }

    public function execute(Spotlight $spotlight, Campaign $campaign)
    {
        if ($campaign->isSent() || $campaign->isSending() || $campaign->isCancelled()) {
            $spotlight->redirect(route('mailcoach.campaigns.summary', $campaign));
        } else if ($campaign->isScheduled()) {
            $spotlight->redirect(route('mailcoach.campaigns.delivery', $campaign));
        } else {
            $spotlight->redirect(route('mailcoach.campaigns.content', $campaign));
        }
    }
}
