<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Spotlight;

use LivewireUI\Spotlight\Spotlight;
use LivewireUI\Spotlight\SpotlightCommand;

class CampaignsCommand extends SpotlightCommand
{
    public function getName(): string
    {
        return __('mailcoach - Campaigns');
    }

    public function execute(Spotlight $spotlight)
    {
        $spotlight->redirect(route('mailcoach.campaigns'));
    }
}
