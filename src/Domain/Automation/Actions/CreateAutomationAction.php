<?php

namespace Spatie\Mailcoach\Domain\Automation\Actions;

use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CreateAutomationAction
{
    use UsesMailcoachModels;

    public function execute(array $attributes): Automation
    {
        $automation = $this->getAutomationClass()::create([
            'name' => $attributes['name'],
        ]);

        return $automation;
    }
}
