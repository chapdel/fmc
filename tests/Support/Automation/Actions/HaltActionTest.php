<?php

namespace Spatie\Mailcoach\Tests\Support\Automation\Actions;

use Spatie\Mailcoach\Models\Subscriber;
use Spatie\Mailcoach\Support\Automation\Actions\HaltAction;
use Spatie\Mailcoach\Tests\TestCase;

class HaltActionTest extends TestCase
{
    /** @test * */
    public function it_halts_the_automation()
    {
        $action = new HaltAction();

        $this->assertTrue($action->shouldHalt(Subscriber::factory()->create()));
    }

    /** @test * */
    public function it_wont_continue()
    {
        $action = new HaltAction();

        $this->assertFalse($action->shouldContinue(Subscriber::factory()->create()));
    }
}
