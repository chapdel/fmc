<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Spotlight;

use LivewireUI\Spotlight\Spotlight;
use LivewireUI\Spotlight\SpotlightCommand;

class AutomationsCommand extends SpotlightCommand
{
    public function getName(): string
    {
        return __('mailcoach - Automations');
    }

    public function execute(Spotlight $spotlight)
    {
        $spotlight->redirect(route('mailcoach.automations'));
    }
}
