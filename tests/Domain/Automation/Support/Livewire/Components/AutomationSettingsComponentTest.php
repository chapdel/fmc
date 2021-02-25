<?php

namespace Spatie\Mailcoach\Tests\Domain\Automation\Support\Livewire\Components;

use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\UnsubscribeAction;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\SubscribedTrigger;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\WebhookTrigger;
use Spatie\Mailcoach\Tests\TestCase;

class AutomationSettingsComponentTest extends TestCase
{
    /** @test * */
    public function it_can_start_and_pause_an_automation()
    {
        /** @var Automation $automation */
        $automation = Automation::factory()->create();
        $automation->chain([
            new UnsubscribeAction(),
        ]);

        Livewire::test('automation-settings', [
            'automation' => $automation,
        ])->assertViewHas('triggerOptions', collect(config('mailcoach.automation.flows.triggers'))
            ->mapWithKeys(function (string $trigger) {
                return [$trigger => $trigger::getName()];
            }))
        ->assertViewHas('emailLists', EmailList::with('segments')->get())
        ->assertViewHas('segmentsData')
        ->assertViewHas('selectedTrigger', SubscribedTrigger::class)
        ->call('setSelectedTrigger', WebhookTrigger::class)
        ->assertViewHas('selectedTrigger', WebhookTrigger::class);
    }
}
