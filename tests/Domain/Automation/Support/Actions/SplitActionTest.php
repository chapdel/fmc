<?php

namespace Spatie\Mailcoach\Tests\Domain\Automation\Support\Actions;

use Illuminate\Support\Carbon;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\SendAutomationMailAction;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\SplitAction;
use Spatie\Mailcoach\Tests\Factories\SubscriberFactory;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\TestTime\TestTime;

class SplitActionTest extends TestCase
{
    protected AutomationMail $automationMail1;

    protected AutomationMail $automationMail2;

    protected Subscriber $subscriber;

    protected Action $actionModel;

    public function setUp(): void
    {
        parent::setUp();

        TestTime::setTestNow(Carbon::create(2021, 01, 01));

        $this->subscriber = SubscriberFactory::new()->confirmed()->create();
        $this->automationMail1 = AutomationMail::factory()->create();
        $this->automationMail2 = AutomationMail::factory()->create();

        $automation = Automation::create()
            ->chain([
                new SplitAction(
                    [
                        new SendAutomationMailAction($this->automationMail1),
                    ],
                    [
                        new SendAutomationMailAction($this->automationMail2),
                    ],
                ),
            ]);

        // Attach a dummy action so we have a pivot table
        $this->actionModel = $automation->actions->first();
        $this->actionModel->subscribers()->attach($this->subscriber);
        $this->subscriber = $this->actionModel->subscribers->first();
    }

    /** @test * */
    public function it_doesnt_halt()
    {
        $this->assertFalse($this->actionModel->action->shouldHalt($this->subscriber));
    }

    /** @test * */
    public function it_determines_the_correct_next_actions()
    {
        $this->assertInstanceOf(SendAutomationMailAction::class, $this->actionModel->action->nextActions($this->subscriber)[0]->action);
        $this->assertEquals($this->automationMail1->id, $this->actionModel->action->nextActions($this->subscriber)[0]->action->automationMail->id);

        $this->assertInstanceOf(SendAutomationMailAction::class, $this->actionModel->action->nextActions($this->subscriber)[1]->action);
        $this->assertEquals($this->automationMail2->id, $this->actionModel->action->nextActions($this->subscriber)[1]->action->automationMail->id);
    }
}
