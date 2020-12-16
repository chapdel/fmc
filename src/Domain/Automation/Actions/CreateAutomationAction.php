<?php

namespace Spatie\Mailcoach\Domain\Automation\Actions;

use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Support\Traits\UsesMailcoachModels;

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
