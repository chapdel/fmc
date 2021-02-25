<?php

namespace Spatie\Mailcoach\Tests\Domain\Automation\Support\Livewire\Components;

use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Automation\Enums\AutomationStatus;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\UnsubscribeAction;
use Spatie\Mailcoach\Tests\TestCase;

class RunAutomationComponentTest extends TestCase
{
    /** @test * */
    public function it_can_start_and_pause_an_automation()
    {
        /** @var Automation $automation */
        $automation = Automation::factory()->create();
        $automation->chain([
            new UnsubscribeAction(),
        ]);

        $this->assertSame(AutomationStatus::PAUSED, $automation->fresh()->status);

        Livewire::test('run-automation', ['automation' => $automation])
            ->call('start');

        $this->assertSame(AutomationStatus::STARTED, $automation->fresh()->status);

        Livewire::test('run-automation', ['automation' => $automation])
            ->call('pause');

        $this->assertSame(AutomationStatus::PAUSED, $automation->fresh()->status);
    }
}
