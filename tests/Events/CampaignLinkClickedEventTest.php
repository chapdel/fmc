<?php

namespace Spatie\Mailcoach\Tests\Events;

use Spatie\Mailcoach\Database\Factories\CampaignSendFactory;
use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Events\CampaignLinkClickedEvent;
use Spatie\Mailcoach\Models\CampaignLink;
use Spatie\Mailcoach\Tests\TestCase;

class CampaignLinkClickedEventTest extends TestCase
{
    /** @test */
    public function it_will_fire_an_event_when_a_link_gets_clicked()
    {
        Event::fake();

        /** @var \Spatie\Mailcoach\Models\Send $send */
        $send = CampaignSendFactory::new()->create();
        $send->campaign->update(['track_clicks' => true]);

        $send->registerClick('https://spatie.be');

        $this->assertCount(1, CampaignLink::get());

        $this->assertDatabaseHas('mailcoach_campaign_links', [
            'campaign_id' => $send->campaign->id,
            'url' => 'https://spatie.be',
        ]);

        $this->assertDatabaseHas('mailcoach_campaign_clicks', [
            'send_id' => $send->id,
            'campaign_link_id' => CampaignLink::first()->id,
            'subscriber_id' => $send->subscriber->id,
        ]);

        Event::assertDispatched(CampaignLinkClickedEvent::class, function (CampaignLinkClickedEvent $event) use ($send) {
            $this->assertEquals($send->uuid, $event->campaignClick->send->uuid);


            return true;
        });
    }

    /** @test */
    public function it_will_not_fire_an_event_when_a_link_gets_clicked_and_click_tracking_is_not_enable()
    {
        Event::fake();

        /** @var \Spatie\Mailcoach\Models\Send $send */
        $send = CampaignSendFactory::new()->create();
        $send->campaign->update(['track_clicks' => false]);

        $send->registerClick('https://spatie.be');

        $this->assertCount(0, CampaignLink::get());

        Event::assertNotDispatched(CampaignLinkClickedEvent::class);
    }
}
