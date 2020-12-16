<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Automations;

use Spatie\Mailcoach\Http\App\Queries\AutomationsQuery;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class AutomationsIndexController
{
    use UsesMailcoachModels;

    public function __invoke(AutomationsQuery $automationsQuery)
    {
        return view('mailcoach::app.automations.index', [
            'automations' => $automationsQuery->paginate(),
            'totalAutomationsCount' => $this->getAutomationClass()::count(),
        ]);
    }
}
