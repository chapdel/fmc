<?php

namespace Spatie\Mailcoach\Actions\Automations;

use Spatie\Mailcoach\Models\Automation;
use Spatie\Mailcoach\Traits\UsesMailcoachModels;

class CreateAutomationAction
{
    use UsesMailcoachModels;

    public function execute(array $attributes): Automation
    {
        $automation = $this->getAutomationClass()::create([
            'name' => $attributes['name'],
        ]);

        cache()->forget('mailcoach-automations');

        return $automation;
    }
}
