<?php

namespace Spatie\Mailcoach\Tests\Domain\Automation\Actions;

use Spatie\Mailcoach\Domain\Automation\Support\Actions\UnsubscribeAction;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Tests\TestCase;

class UnsubscribeActionTest extends TestCase
{
    /** @test * */
    public function it_unsubscribes_the_subscriber()
    {
        $action = new UnsubscribeAction();

        /** @var Subscriber $subscriber */
        $subscriber = Subscriber::factory()->create();

        $this->assertTrue($subscriber->isSubscribed());

        $action->run($subscriber);

        $this->assertFalse($subscriber->fresh()->isSubscribed());
    }

    /** @test * */
    public function it_halts_the_automation()
    {
        $action = new UnsubscribeAction();

        $this->assertTrue($action->shouldHalt(Subscriber::factory()->create()));
    }

    /** @test * */
    public function it_wont_continue()
    {
        $action = new UnsubscribeAction();

        $this->assertFalse($action->shouldContinue(Subscriber::factory()->create()));
    }
}
