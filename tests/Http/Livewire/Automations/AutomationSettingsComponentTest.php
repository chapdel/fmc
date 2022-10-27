<?php

use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\UnsubscribeAction;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\SubscribedTrigger;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\WebhookTrigger;
use Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationSettingsComponent;

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
    ])->assertViewHas('triggerOptions')
    ->assertViewHas('emailLists')
    ->assertViewHas('segmentsData')
    ->assertViewHas('selectedTrigger', SubscribedTrigger::class)
    ->set('selectedTrigger', WebhookTrigger::class)
    ->assertViewHas('selectedTrigger', WebhookTrigger::class);
});
