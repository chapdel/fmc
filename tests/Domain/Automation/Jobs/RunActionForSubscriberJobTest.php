<?php

namespace Spatie\Mailcoach\Tests\Domain\Automation\Jobs;

use Carbon\CarbonInterval;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Automation\Jobs\RunActionForSubscriberJob;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\WaitAction;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\CustomSendAutomationMailAction;
use Spatie\TestTime\TestTime;

class RunActionForSubscriberJobTest extends TestCase
{
    private EmailList $emailList;

    public function setUp(): void
    {
        parent::setUp();

        $this->emailList = EmailList::factory()->create();
    }

    /** @test * */
    public function it_runs_the_action_for_a_subscriber()
    {
        TestTime::freeze();

        $automation = Automation::factory()->create();

        $action = Action::create([
            'automation_id' => $automation->id,
            'action' => new WaitAction(CarbonInterval::day()),
            'order' => 1,
        ]);

        $action2 = Action::create([
            'automation_id' => $automation->id,
            'action' => new WaitAction(CarbonInterval::minute()),
            'order' => 2,
        ]);

        $subscriber = $this->emailList->subscribe('john@doe.com');

        $action->subscribers()->attach($subscriber);

        dispatch_sync(new RunActionForSubscriberJob($action, $subscriber));

        $this->assertEquals($action->id, $subscriber->actions->first()->id);

        TestTime::addDays(2);

        dispatch_sync(new RunActionForSubscriberJob($action, $subscriber));

        $this->assertEquals(2, $subscriber->actions()->count());

        dispatch_sync(new RunActionForSubscriberJob($action, $subscriber));

        // it won't add it twice
        $this->assertEquals(2, $subscriber->actions()->count());
    }

    /** @test * */
    public function it_optionally_passes_the_action_subscriber()
    {
        TestTime::freeze();

        $automation = Automation::factory()->create();
        $automationMail = AutomationMail::factory()->create();

        $action = Action::create([
            'automation_id' => $automation->id,
            'action' => new CustomSendAutomationMailAction($automationMail),
            'order' => 1,
        ]);

        $subscriber = $this->emailList->subscribe('john@doe.com');

        $action->subscribers()->attach($subscriber);

        $this->expectExceptionMessage("ActionSubscriber is set!");

        dispatch_sync(new RunActionForSubscriberJob($action, $subscriber));
    }
}
