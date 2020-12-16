<?php

namespace Spatie\Mailcoach\Tests\Support\Automation\Actions;

use Carbon\CarbonInterval;
use Spatie\Mailcoach\Models\Action;
use Spatie\Mailcoach\Models\Subscriber;
use Spatie\Mailcoach\Support\Automation\Actions\WaitAction;
use Spatie\Mailcoach\Tests\Factories\SubscriberFactory;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\TestTime\TestTime;

class WaitActionTest extends TestCase
{
    private Subscriber $subscriber;

    public function setUp(): void
    {
        parent::setUp();

        $this->action = Action::factory()->create();
        $this->action->subscribers()->attach(SubscriberFactory::new()->create());

        $this->subscriber = $this->action->subscribers->first();

        TestTime::freeze();
    }

    /** @test * */
    public function it_never_halts_the_automation()
    {
        $action = new WaitAction(CarbonInterval::days(1));

        $this->assertFalse($action->shouldHalt($this->subscriber));

        TestTime::addDay();

        $this->assertFalse($action->shouldHalt($this->subscriber));
    }

    /** @test * */
    public function it_will_only_continue_when_time_has_passed()
    {
        $action = new WaitAction(CarbonInterval::days(1));

        $this->assertFalse($action->shouldContinue($this->subscriber));

        TestTime::addDay();

        $this->assertTrue($action->shouldContinue($this->subscriber));
    }
}
