<?php


namespace Spatie\Mailcoach\Tests\Domain\Automation\Actions;

use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Automation\Actions\ShouldAutomationRunForSubscriberAction;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\SendAutomationMailAction;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\SubscribedTrigger;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\TestSegmentQueryOnlyJohn;

class ShouldAutomationRunForSubscriberActionTest extends TestCase
{
    private EmailList $emailList;

    private AutomationMail $automationMail;
    private ShouldAutomationRunForSubscriberAction $action;

    public function setUp(): void
    {
        parent::setUp();

        $this->emailList = EmailList::factory()->create();
        $this->automationMail = AutomationMail::factory()->create();
        $this->action = resolve(ShouldAutomationRunForSubscriberAction::class);

        Queue::fake();
    }

    /** @test * */
    public function it_returns_false_when_the_subscriber_is_already_in_the_automation()
    {
        /** @var Automation $automation */
        $automation = Automation::create()
            ->name('New year!')
            ->runEvery(CarbonInterval::minute())
            ->to($this->emailList)
            ->triggerOn(new SubscribedTrigger())
            ->chain([
                new SendAutomationMailAction($this->automationMail),
            ])->start();

        $this->refreshServiceProvider();

        $this->assertEmpty($automation->actions->first()->fresh()->subscribers);

        $jane = $this->emailList->subscribeSkippingConfirmation('jane@doe.com');

        $this->assertTrue($this->action->execute($automation, $jane));

        $automation->actions->first()->subscribers()->attach($jane);

        $this->assertFalse($this->action->execute($automation, $jane));
    }

    /** @test * */
    public function it_returns_false_if_the_subscriber_isnt_subscribed()
    {
        /** @var Automation $automation */
        $automation = Automation::create()
            ->name('New year!')
            ->runEvery(CarbonInterval::minute())
            ->to($this->emailList)
            ->triggerOn(new SubscribedTrigger())
            ->chain([
                new SendAutomationMailAction($this->automationMail),
            ])->start();

        $this->refreshServiceProvider();

        $this->assertEmpty($automation->actions->first()->fresh()->subscribers);

        $jane = $this->emailList->subscribeSkippingConfirmation('jane@doe.com');

        $this->assertTrue($this->action->execute($automation, $jane));

        $jane->unsubscribe();

        $this->assertFalse($this->action->execute($automation, $jane));
    }

    /** @test * */
    public function it_returns_false_when_the_subscriber_isnt_in_the_segment()
    {
        /** @var Automation $automation */
        $automation = Automation::create()
            ->name('New year!')
            ->runEvery(CarbonInterval::minute())
            ->to($this->emailList)
            ->segment(TestSegmentQueryOnlyJohn::class)
            ->triggerOn(new SubscribedTrigger())
            ->chain([
                new SendAutomationMailAction($this->automationMail),
            ])->start();

        $this->refreshServiceProvider();

        $jane = $this->emailList->subscribeSkippingConfirmation('jane@doe.com');
        $john = $this->emailList->subscribeSkippingConfirmation('john@example.com');

        $this->assertEquals(0, $automation->actions()->first()->subscribers->count());

        $this->assertFalse($this->action->execute($automation, $jane));
        $this->assertTrue($this->action->execute($automation, $john));
    }
}
