<?php

namespace Spatie\Mailcoach\Tests\Domain\Automation\Jobs;

use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Automation\Jobs\RunAutomationForSubscriberJob;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\SendAutomationMailAction;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\SubscribedTrigger;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\CustomShouldAutomationRunForSubscriberAction;

class RunAutomationForSubscriberJobTest extends TestCase
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
    public function it_runs_the_automation_for_a_subscriber()
    {
        /** @var Automation $automation */
        $automation = Automation::create()
            ->name('New year!')
            ->runEvery(CarbonInterval::minute())
            ->to($this->emailList)
            ->triggerOn(new SubscribedTrigger())
            ->chain([
                new SendAutomationMailAction($this->automationMail),
            ])
            ->start();

        $this->refreshServiceProvider();

        $this->assertEmpty($automation->actions->first()->fresh()->subscribers);

        $this->assertEquals(0, $automation->actions()->first()->subscribers->count());

        $jane = $this->emailList->subscribeSkippingConfirmation('jane@doe.com');

        (new RunAutomationForSubscriberJob($automation, $jane))->handle();

        $this->assertEquals(1, $automation->actions()->first()->subscribers->count());
    }

    /** @test * */
    public function it_does_nothing_when_the_automation_isnt_started()
    {
        /** @var Automation $automation */
        $automation = Automation::create()
            ->name('New year!')
            ->runEvery(CarbonInterval::minute())
            ->to($this->emailList)
            ->triggerOn(new SubscribedTrigger())
            ->chain([
                new SendAutomationMailAction($this->automationMail),
            ]);

        $this->refreshServiceProvider();

        $this->assertEmpty($automation->actions->first()->fresh()->subscribers);

        $jane = $this->emailList->subscribeSkippingConfirmation('jane@doe.com');

        $this->assertEquals(0, $automation->actions()->first()->subscribers->count());

        (new RunAutomationForSubscriberJob($automation, $jane))->handle();

        $this->assertEquals(0, $automation->actions()->first()->subscribers->count());
    }

    /** @test * */
    public function it_uses_the_configured_action()
    {
        config()->set('mailcoach.automation.actions.should_run_for_subscriber', CustomShouldAutomationRunForSubscriberAction::class);

        /** @var Automation $automation */
        $automation = Automation::create()
            ->name('New year!')
            ->runEvery(CarbonInterval::minute())
            ->to($this->emailList)
            ->triggerOn(new SubscribedTrigger())
            ->chain([
                new SendAutomationMailAction($this->automationMail),
            ])
            ->start();

        $this->refreshServiceProvider();

        $this->assertEmpty($automation->actions->first()->fresh()->subscribers);

        $jane = $this->emailList->subscribeSkippingConfirmation('jane@doe.com');

        $this->assertEquals(0, $automation->actions()->first()->subscribers->count());

        $this->expectExceptionMessage("CustomShouldAutomationRunForSubscriberAction was used");

        (new RunAutomationForSubscriberJob($automation, $jane))->handle();
    }
}
