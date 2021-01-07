<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Livewire\Triggers;

use Spatie\Mailcoach\Domain\Automation\Support\Livewire\AutomationTriggerComponent;

class WebhookTriggerComponent extends AutomationTriggerComponent
{
    public function render()
    {
        return <<<'blade'
            <div class="form-row">
                <p>Send an authenticated <code>POST</code> request to the following endpoint, make sure you've set up the <a class="link" href="https://spatie.be/docs/laravel-mailcoach/v4/api/introduction">Mailcoach API</a>.</p>
                <p class="max-w-full overflow-x-auto"><code class="whitespace-no-wrap">{{ action(\Spatie\Mailcoach\Http\Api\Controllers\Automations\TriggerAutomationController::class, [$this->automation, '::subscriber_id::']) }}</code></p>
            </div>
        blade;
    }
}
