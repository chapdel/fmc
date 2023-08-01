<?php

use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\UnsubscribeAction;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\SubscribedTrigger;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\WebhookTrigger;
use Spatie\Mailcoach\Livewire\Automations\AutomationSettingsComponent;

it('can change automation settings', function () {
    $this->authenticate();

    /** @var Automation $automation */
    $automation = Automation::factory()->create();
    $automation->triggerOn(new SubscribedTrigger());
    $automation->chain([
        new UnsubscribeAction(),
    ]);

    Livewire::test(AutomationSettingsComponent::class, [
        'automation' => $automation,
    ])->assertSet('selectedTrigger', SubscribedTrigger::class)
        ->set('selectedTrigger', WebhookTrigger::class)
        ->assertSet('selectedTrigger', WebhookTrigger::class);
});
