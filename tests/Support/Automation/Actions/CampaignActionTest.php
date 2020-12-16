<?php

namespace Spatie\Mailcoach\Tests\Support\Automation\Actions;

use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignToSubscriberJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\CampaignAction;
use Spatie\Mailcoach\Tests\Factories\CampaignFactory;
use Spatie\Mailcoach\Tests\Factories\SubscriberFactory;
use Spatie\Mailcoach\Tests\TestCase;

class CampaignActionTest extends TestCase
{
    private Subscriber $subscriber;

    private Campaign $campaign;

    private CampaignAction $action;

    public function setUp(): void
    {
        parent::setUp();

        $this->subscriber = SubscriberFactory::new()->confirmed()->create();
        $this->campaign = (new CampaignFactory())->create();
        $this->campaign->update(['email_list_id' => $this->subscriber->email_list_id]);
        $this->action = new CampaignAction($this->campaign);
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
    public function it_sends_a_campaign_to_the_subscriber()
    {
        Queue::fake();

        $this->action->run($this->subscriber);

        Queue::assertPushed(SendCampaignToSubscriberJob::class, function (SendCampaignToSubscriberJob $sendCampaignJob) {
            $this->assertTrue($this->subscriber->is($sendCampaignJob->subscriber));
            $this->assertTrue($this->campaign->is($sendCampaignJob->campaign));

            return true;
        });
    }

    /** @test * */
    public function it_wont_send_a_campaign_twice()
    {
        $this->action->run($this->subscriber);

        $this->action->run($this->subscriber);

        $this->assertEquals(1, $this->campaign->sends->count());
    }
}
