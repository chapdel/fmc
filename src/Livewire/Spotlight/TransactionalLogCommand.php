<?php

namespace Spatie\Mailcoach\Livewire\Spotlight;

use LivewireUI\Spotlight\Spotlight;
use LivewireUI\Spotlight\SpotlightCommand;

class TransactionalLogCommand extends SpotlightCommand
{
    public function getName(): string
    {
        return __mc('Transactional log');
    }

    public function execute(Spotlight $spotlight)
    {
        $spotlight->redirect(route('mailcoach.transactionalMails'));
    }
}
