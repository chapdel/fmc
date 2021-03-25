<?php

namespace Spatie\Mailcoach\Tests\Domain\Automation\Support\Actions;

use Carbon\CarbonInterval;
use Illuminate\Support\Carbon;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\ConditionAction;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\HaltAction;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\SendAutomationMailAction;
use Spatie\Mailcoach\Domain\Automation\Support\Conditions\HasTagCondition;
use Spatie\Mailcoach\Tests\Factories\SubscriberFactory;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\TestTime\TestTime;

class ConditionActionTest extends TestCase
{
    protected AutomationMail $automationMail;

    protected Subscriber $subscriber;

    protected Action $actionModel;

    public function setUp(): void
    {
        parent::setUp();

        TestTime::setTestNow(Carbon::create(2021, 01, 01));

        $this->subscriber = SubscriberFactory::new()->confirmed()->create();
        $this->automationMail = AutomationMail::factory()->create();

        $automation = Automation::create()
            ->chain([
                new ConditionAction(
                    CarbonInterval::day(),
                    [
                        new SendAutomationMailAction($this->automationMail),
                    ],
                    [
                        new HaltAction(),
                    ],
                    HasTagCondition::class,
                    ['tag' => 'some-tag']
                ),
            ]);

        // Attach a dummy action so we have a pivot table
        $this->actionModel = $automation->actions->first();
        $this->actionModel->subscribers()->attach($this->subscriber);
        $this->subscriber = $this->actionModel->subscribers->first();
    }

    /** @test * */
    public function it_doesnt_continue_while_checking_and_the_subscriber_doesnt_have_the_tag()
    {
        $this->assertFalse($this->actionModel->action->shouldContinue($this->subscriber));

        TestTime::addDay();

        $this->assertFalse($this->actionModel->action->shouldContinue($this->subscriber));

        TestTime::addSecond();

        $this->assertTrue($this->actionModel->action->shouldContinue($this->subscriber));
    }

    /** @test * */
    public function it_continues_as_soon_as_the_subscriber_has_the_tag()
    {
        $this->assertFalse($this->actionModel->action->shouldContinue($this->subscriber));

        $this->subscriber->addTag('some-tag');

        $this->assertTrue($this->actionModel->action->shouldContinue($this->subscriber));
    }

    /** @test * */
    public function it_doesnt_halt()
    {
        $this->assertFalse($this->actionModel->action->shouldHalt($this->subscriber));
    }

    /** @test * */
    public function it_determines_the_correct_next_action()
    {
        TestTime::addDays(2);

        $this->assertInstanceOf(HaltAction::class, $this->actionModel->action->nextActions($this->subscriber)[0]->action);

        $this->subscriber->addTag('some-tag');

        $this->assertInstanceOf(SendAutomationMailAction::class, $this->actionModel->action->nextActions($this->subscriber)[0]->action);
    }
}