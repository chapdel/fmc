<?php

namespace Spatie\Mailcoach\Tests\Domain\Automation\Actions;

use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Domain\Automation\Jobs\SendAutomationMailToSubscriberJob;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\SendAutomationMailAction;
use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;
use Spatie\Mailcoach\Tests\Factories\SubscriberFactory;
use Spatie\Mailcoach\Tests\TestCase;

class SendAutomationMailActionTest extends TestCase
{
    protected Subscriber $subscriber;

    protected AutomationMail $automationMail;

    protected SendAutomationMailAction $action;

    public function setUp(): void
    {
        parent::setUp();

        $this->subscriber = SubscriberFactory::new()->confirmed()->create();
        $this->automationMail = AutomationMail::factory()->create();
        $this->action = new SendAutomationMailAction($this->automationMail);
    }

    /** @test * */
    public function it_continues_after_execution()
    {
        $this->assertTrue($this->action->shouldContinue($this->subscriber));
    }

    /** @test * */
    public function it_wont_halt_after_execution()
    {
        $this->assertFalse($this->action->shouldHalt($this->subscriber));
    }

    /** @test * */
    public function it_sends_an_automation_mail_to_the_subscriber()
    {
        Queue::fake();

        $this->action->run($this->subscriber);

        Queue::assertPushed(SendAutomationMailToSubscriberJob::class, function (SendAutomationMailToSubscriberJob $sendCampaignJob) {
            $this->assertTrue($this->subscriber->is($sendCampaignJob->subscriber));
            $this->assertTrue($this->automationMail->is($sendCampaignJob->automationMail));

            return true;
        });
    }

    /** @test * */
    public function it_wont_send_a_campaign_twice()
    {
        $this->action->run($this->subscriber);

        $this->action->run($this->subscriber);

        $this->assertEquals(1, $this->automationMail->sends->count());
    }
}
