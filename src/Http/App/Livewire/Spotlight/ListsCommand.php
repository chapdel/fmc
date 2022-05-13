<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Spotlight;

use LivewireUI\Spotlight\Spotlight;
use LivewireUI\Spotlight\SpotlightCommand;

class ListsCommand extends SpotlightCommand
{
    public function getName(): string
    {
        return __('mailcoach - Email lists');
    }

    public function execute(Spotlight $spotlight)
    {
        $spotlight->redirect(route('mailcoach.emailLists'));
    }
}
