<?php

namespace Spatie\Mailcoach\Tests\Domain\Campaign\Events;

use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignSentEvent;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignJob;
use Spatie\Mailcoach\Tests\Factories\CampaignFactory;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\TestMailcoachMail;

class CampaignSentEventTest extends TestCase
{
    /** @test */
    public function it_fires_an_event_after_a_campaign_has_been_sent()
    {
        Event::fake(CampaignSentEvent::class);

        $campaign = (new CampaignFactory())
            ->withSubscriberCount(3)
            ->mailable(TestMailcoachMail::class)
            ->create();

        $campaign->content($campaign->contentFromMailable());

        dispatch(new SendCampaignJob($campaign));

        Event::assertDispatched(CampaignSentEvent::class, function (CampaignSentEvent $event) use ($campaign) {
            $this->assertEquals($campaign->id, $event->campaign->id);

            return true;
        });
    }
}
