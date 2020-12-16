<?php

namespace Spatie\Mailcoach\Tests\Events;

use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Database\Factories\CampaignSendFactory;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignMailSentEvent;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendMailJob;
use Spatie\Mailcoach\Tests\TestCase;

class CampaignMailSentEventTest extends TestCase
{
    /** @test */
    public function it_will_fire_an_event_when_the_mail_is_sent()
    {
        Event::fake(CampaignMailSentEvent::class);

        $send = CampaignSendFactory::new()->create();

        dispatch(new SendMailJob($send));

        Event::assertDispatched(CampaignMailSentEvent::class, function (CampaignMailSentEvent $event) use ($send) {
            $this->assertEquals($send->uuid, $event->send->uuid);

            return true;
        });
    }
}
