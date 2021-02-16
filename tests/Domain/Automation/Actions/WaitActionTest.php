<?php

namespace Spatie\Mailcoach\Tests\Domain\Automation\Actions;

use Carbon\CarbonInterval;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\WaitAction;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Tests\Factories\SubscriberFactory;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\TestTime\TestTime;

class WaitActionTest extends TestCase
{
    protected Subscriber $subscriber;

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
