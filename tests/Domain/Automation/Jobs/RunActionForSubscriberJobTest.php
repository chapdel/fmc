<?php

namespace Spatie\Mailcoach\Tests\Domain\Automation\Jobs;

use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Automation\Jobs\RunActionForSubscriberJob;
use Spatie\Mailcoach\Domain\Automation\Jobs\RunAutomationForSubscriberJob;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\SendAutomationMailAction;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\WaitAction;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\SubscribedTrigger;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\TestSegmentQueryOnlyJohn;
use Spatie\TestTime\TestTime;

class RunActionForSubscriberJobTest extends TestCase
{
    private EmailList $emailList;

    private AutomationMail $automationMail;

    public function setUp(): void
    {
        parent::setUp();

        $this->emailList = EmailList::factory()->create();
        $this->automationMail = AutomationMail::factory()->create();

        Queue::fake();
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

        $job = (new RunActionForSubscriberJob($action, $subscriber));
        $job->handle();

        $this->assertEquals($action->id, $subscriber->actions->first()->id);

        TestTime::addDays(2);
        $job->handle();

        $this->assertEquals(2, $subscriber->actions()->count());

        $job->handle();

        // it won't add it twice
        $this->assertEquals(2, $subscriber->actions()->count());
    }
}
