<?php

use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\UnsubscribeAction;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\SubscribedTrigger;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\WebhookTrigger;

it('can change automation settings', function () {
    /** @var Automation $automation */
    $automation = Automation::factory()->create();
    $automation->triggerOn(new SubscribedTrigger());
    $automation->chain([
        new UnsubscribeAction(),
    ]);

    Livewire::test('automation-settings', [
        'automation' => $automation,
    ])->assertViewHas('triggerOptions')
    ->assertViewHas('emailLists', EmailList::with('segments')->get())
    ->assertViewHas('segmentsData')
    ->assertViewHas('selectedTrigger', SubscribedTrigger::class)
    ->call('setSelectedTrigger', WebhookTrigger::class)
    ->assertViewHas('selectedTrigger', WebhookTrigger::class);
});
