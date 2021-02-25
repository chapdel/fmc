<?php

namespace Spatie\Mailcoach\Tests\Domain\Automation\Actions;

use Illuminate\Support\Facades\Cache;
use Spatie\Mailcoach\Domain\Automation\Actions\CreateAutomationAction;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Tests\TestCase;

class CreateAutomationActionTest extends TestCase
{
    /** @test * */
    public function it_creates_an_automation()
    {
        $action = resolve(CreateAutomationAction::class);

        $action->execute([
            'name' => 'Some automation',
        ]);

        $this->assertEquals(1, Automation::count());
    }

    /** @test * */
    public function it_clears_the_automation_cache()
    {
        Cache::spy()->shouldReceive('forget')
            ->with('mailcoach-automations')
            ->atLeast()
            ->once();

        $action = resolve(CreateAutomationAction::class);

        $action->execute([
            'name' => 'Some automation',
        ]);

        $this->assertEquals(1, Automation::count());
    }
}
