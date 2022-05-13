<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Spotlight;

use LivewireUI\Spotlight\Spotlight;
use LivewireUI\Spotlight\SpotlightCommand;

class TransactionalLogCommand extends SpotlightCommand
{
    public function getName(): string
    {
        return __('mailcoach - Transactional log');
    }

    public function execute(Spotlight $spotlight)
    {
        $spotlight->redirect(route('mailcoach.transactionalMails'));
    }
}
